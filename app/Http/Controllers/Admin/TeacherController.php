<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class TeacherController extends Controller{
 public function index(Request $r){
  $p=$r->attributes->get('academic_period');
  $q=DB::table('teachers as t')->leftJoin('users as u','u.teacher_id','=','t.id')->select('t.*','u.id as user_id','u.login','u.email');
  if($r->filled('q')){$s='%'.$r->q.'%';$q->where(fn($x)=>$x->where('t.last_name','like',$s)->orWhere('t.first_name','like',$s)->orWhere('t.middle_name','like',$s));}
  if($r->status==='active')$q->where('t.active',1);if($r->status==='archive')$q->where('t.active',0);
  $teachers=$q->orderBy('t.last_name')->orderBy('t.first_name')->paginate(50)->withQueryString();
  if($p){
   foreach($teachers as $t){
    $loads=DB::table('teaching_load_plans')->where(['academic_period_id'=>$p->id,'teacher_id'=>$t->id])->where('status','active');
    $t->load_hours=(float)$loads->sum('hours_total');$t->load_rows=$loads->count();
    $lessons=DB::table('lesson_occurrences')->where(['academic_period_id'=>$p->id,'teacher_id'=>$t->id])->where('status','!=','cancelled');
    $t->lessons=$lessons->count();$t->completed_lessons=(clone $lessons)->whereDate('lesson_date','<=',now()->toDateString())->count();
    $t->missing_topics=(clone $lessons)->whereDate('lesson_date','<=',now()->toDateString())->where(fn($x)=>$x->whereNull('topic')->orWhere('topic',''))->count();
    $ids=(clone $lessons)->whereDate('lesson_date','<=',now()->toDateString())->pluck('id');
    $marked=DB::table('attendance_records')->whereIn('lesson_occurrence_id',$ids)->distinct()->count('lesson_occurrence_id');
    $t->missing_attendance=max(0,$t->completed_lessons-$marked);
    $t->open_notes=DB::table('journal_review_notes')->where(['academic_period_id'=>$p->id,'teacher_id'=>$t->id,'status'=>'open'])->count();
    $t->curator_groups=DB::table('study_groups')->where(['curator_teacher_id'=>$t->id,'active'=>1])->pluck('name')->implode(', ');
   }
  }
  $canManage=$r->user()->isSuperAdmin()||$r->user()->hasRole('admin')||$r->user()->canDo('users.manage');
  return view('admin.teachers.index',compact('teachers','p','canManage'));
 }
 public function create(){return view('admin.teachers.form',['teacher'=>null,'account'=>null,'loads'=>collect(),'groups'=>collect(),'methodical'=>null]);}
 public function store(Request $r){$d=$this->validated($r);$id=DB::table('teachers')->insertGetId($d+['active'=>1,'created_at'=>now(),'updated_at'=>now()]);return redirect()->route('admin.teachers.edit',$id)->with('success','Преподаватель добавлен');}
 public function edit($id){$t=DB::table('teachers')->find($id);abort_if(!$t,404);$account=DB::table('users')->where('teacher_id',$id)->first();$p=DB::table('academic_periods')->where('is_current',1)->first()?:DB::table('academic_periods')->orderByDesc('starts_on')->first();$loads=$p?DB::table('teaching_load_plans as l')->leftJoin('study_groups as g','g.id','=','l.study_group_id')->leftJoin('subjects as s','s.id','=','l.subject_id')->where(['l.teacher_id'=>$id,'l.academic_period_id'=>$p->id])->select('l.*','g.name as group_name','s.name as subject_name')->get():collect();$groups=DB::table('study_groups')->where('curator_teacher_id',$id)->where('active',1)->orderBy('name')->get();$methodical=DB::table('teacher_methodical_plans')->where('teacher_id',$id)->when($p,fn($q)=>$q->where('academic_period_id',$p->id))->first();return view('admin.teachers.form',['teacher'=>$t,'account'=>$account,'loads'=>$loads,'groups'=>$groups,'methodical'=>$methodical]);}
 public function update(Request $r,$id){abort_unless(DB::table('teachers')->where('id',$id)->exists(),404);DB::table('teachers')->where('id',$id)->update($this->validated($r)+['updated_at'=>now()]);return back()->with('success','Карточка преподавателя сохранена');}
 public function destroy($id){abort_unless(DB::table('teachers')->where('id',$id)->exists(),404);DB::table('teachers')->where('id',$id)->update(['active'=>0,'updated_at'=>now()]);DB::table('users')->where('teacher_id',$id)->update(['is_active'=>0,'updated_at'=>now()]);return redirect()->route('admin.teachers.index')->with('success','Преподаватель и его доступ переведены в архив');}
 private function validated(Request $r){return $r->validate(['last_name'=>'required|string|max:100','first_name'=>'required|string|max:100','middle_name'=>'nullable|string|max:100']);}
}