<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\PeriodGuard;

class AcademicOfficeController extends Controller
{
    private function access(Request $r): void
    {
        abort_unless($r->user()->isSuperAdmin() || $r->user()->hasRole('admin') || $r->user()->hasRole('academic_head') || $r->user()->canDo('students.manage'), 403);
    }

    private function period(Request $r)
    {
        return $r->attributes->get('academic_period');
    }

    public function index(Request $r)
    {
        $this->access($r);
        $p = $this->period($r);
        $groups = Schema::hasTable('study_groups') ? DB::table('study_groups')->where('active',1)->orderBy('name')->get() : collect();
        $students = Schema::hasTable('students') ? DB::table('students')->where('status','active')->orderBy('last_name')->orderBy('first_name')->get() : collect();
        $practices = collect();
        if (Schema::hasTable('student_practices')) {
            $practices = DB::table('student_practices as p')->leftJoin('students as s','s.id','=','p.student_id')
                ->select('p.*',DB::raw("trim(coalesce(s.last_name,'') || ' ' || coalesce(s.first_name,'')) as student_name"))
                ->when($p && Schema::hasColumn('student_practices','academic_period_id'),fn($q)=>$q->where('p.academic_period_id',$p->id))
                ->orderByDesc('p.id')->limit(100)->get();
        }
        $orders = Schema::hasTable('academic_orders') ? DB::table('academic_orders')->orderByDesc('order_date')->orderByDesc('id')->limit(100)->get() : collect();
        return view('academic-office.index',compact('p','groups','students','practices','orders'));
    }

    public function news(Request $r)
    {
        $this->access($r); $p=$this->period($r); PeriodGuard::writable($p->id);
        abort_unless(Schema::hasTable('academic_news'),503,'Таблица academic_news ещё не создана.');
        $d=$r->validate(['study_group_id'=>'nullable|integer','title'=>'required|string|max:255','body'=>'required|string|max:10000','pinned'=>'nullable|boolean']);
        DB::table('academic_news')->insert(array_merge($d,['academic_period_id'=>$p->id,'created_by'=>$r->user()->id,'pinned'=>$r->boolean('pinned'),'created_at'=>now(),'updated_at'=>now()]));
        return back()->with('success','Новость опубликована.');
    }

    public function activity(Request $r)
    {
        $this->access($r); $p=$this->period($r); PeriodGuard::writable($p->id);
        abort_unless(Schema::hasTable('student_activities'),503,'Таблица student_activities ещё не создана.');
        $d=$r->validate(['study_group_id'=>'nullable|integer','title'=>'required|string|max:255','description'=>'nullable|string','location'=>'nullable|string|max:255','starts_at'=>'required|date','registration_until'=>'nullable|date','capacity'=>'nullable|integer|min:1']);
        DB::table('student_activities')->insert(array_merge($d,['academic_period_id'=>$p->id,'created_by'=>$r->user()->id,'created_at'=>now(),'updated_at'=>now()]));
        return back()->with('success','Активность создана.');
    }

    public function practice(Request $r)
    {
        $this->access($r); $p=$this->period($r); PeriodGuard::writable($p->id);
        abort_unless(Schema::hasTable('student_practices'),503,'Таблица student_practices ещё не создана.');
        $d=$r->validate(['student_id'=>'required|integer','practice_type'=>'required|string|max:255','organization_name'=>'required|string|max:255','organization_address'=>'nullable|string|max:255','supervisor_org'=>'nullable|string|max:255','starts_on'=>'required|date','ends_on'=>'required|date|after_or_equal:starts_on','order_number'=>'nullable|string|max:100','order_date'=>'nullable|date']);
        DB::table('student_practices')->insert(array_merge($d,['academic_period_id'=>$p->id,'status'=>'planned','created_at'=>now(),'updated_at'=>now()]));
        return back()->with('success','Практика назначена.');
    }

    public function practiceResult(Request $r, $practice)
    {
        $this->access($r); $p=$this->period($r); PeriodGuard::writable($p->id);
        abort_unless(Schema::hasTable('student_practices'),404);
        $d=$r->validate(['status'=>'required|string|max:50','result'=>'nullable|string|max:255','grade'=>'nullable|string|max:20']);
        DB::table('student_practices')->where('id',$practice)->update(array_merge($d,['updated_at'=>now()]));
        return back()->with('success','Результат практики сохранён.');
    }

    public function order(Request $r)
    {
        $this->access($r); $p=$this->period($r); PeriodGuard::writable($p->id);
        abort_unless(Schema::hasTable('academic_orders'),503,'Таблица academic_orders ещё не создана.');
        $d=$r->validate(['number'=>'required|string|max:100','order_date'=>'required|date','student_id'=>'nullable|integer','type'=>'required|string|max:100','title'=>'required|string|max:500','basis'=>'nullable|string|max:5000']);
        DB::table('academic_orders')->insert(array_merge($d,['academic_period_id'=>$p->id,'created_by'=>$r->user()->id,'created_at'=>now(),'updated_at'=>now()]));
        return back()->with('success','Приказ зарегистрирован.');
    }
}
