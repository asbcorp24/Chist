<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class StudentController extends Controller {
 public function index(Request $r){$q=DB::table('students as s')->leftJoin('study_groups as g','g.id','=','s.current_group_id')->select('s.*','g.name as group_name');if($r->group)$q->where('g.id',$r->group);if($r->q){$x='%'.$r->q.'%';$q->where(fn($w)=>$w->where('s.last_name','like',$x)->orWhere('s.first_name','like',$x));}return view('admin.students.index',['students'=>$q->orderBy('s.last_name')->paginate(40)->withQueryString(),'groups'=>DB::table('study_groups')->where('active',1)->where('is_visible',1)->orderBy('name')->get()]);}
 public function create(){return view('admin.students.form',['student'=>null,'groups'=>DB::table('study_groups')->where('active',1)->orderBy('name')->get()]);}
 public function store(Request $r){$d=$this->validated($r);$id=DB::table('students')->insertGetId($d+['created_at'=>now(),'updated_at'=>now()]);$this->membership($id,$d['current_group_id']);return redirect()->route('admin.students.index')->with('success','Студент добавлен');}
 public function edit($id){$s=DB::table('students')->find($id);abort_if(!$s,404);return view('admin.students.form',['student'=>$s,'groups'=>DB::table('study_groups')->where('active',1)->orderBy('name')->get()]);}
 public function update(Request $r,$id){$d=$this->validated($r);DB::table('students')->where('id',$id)->update($d+['updated_at'=>now()]);$this->membership($id,$d['current_group_id']);return back()->with('success','Карточка сохранена');}
 private function validated(Request $r){return $r->validate(['current_group_id'=>'required|exists:study_groups,id','last_name'=>'required|max:100','first_name'=>'required|max:100','middle_name'=>'nullable|max:100','status'=>'required|in:active,academic_leave,expelled,graduated,transferred','enrolled_on'=>'nullable|date','left_on'=>'nullable|date']);}
 private function membership($studentId,$groupId){$period=DB::table('academic_periods')->where('is_current',1)->first()?:DB::table('academic_periods')->orderByDesc('starts_on')->first();if($period)DB::table('student_group_memberships')->updateOrInsert(['student_id'=>$studentId,'academic_period_id'=>$period->id],['study_group_id'=>$groupId,'status'=>'active','updated_at'=>now(),'created_at'=>now()]);}
}
