<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class GroupController extends Controller {
 public function index(){return view('admin.groups.index',['groups'=>DB::table('study_groups as g')->leftJoin('teachers as t','t.id','=','g.curator_teacher_id')->select('g.*',DB::raw("trim(coalesce(t.last_name,'')||' '||coalesce(t.first_name,'')||' '||coalesce(t.middle_name,'')) as curator"))->orderBy('g.name')->get()]);}
 public function edit($id){$g=DB::table('study_groups')->find($id);abort_if(!$g,404);return view('admin.groups.form',['group'=>$g,'teachers'=>DB::table('teachers')->where('active',1)->orderBy('last_name')->get(),'students'=>DB::table('students')->where('current_group_id',$id)->where('status','active')->orderBy('last_name')->get()]);}
 public function update(Request $r,$id){$r->validate(['curator_teacher_id'=>'nullable|exists:teachers,id','starosta_student_id'=>'nullable|exists:students,id']);if($r->starosta_student_id)abort_unless(DB::table('students')->where('id',$r->starosta_student_id)->where('current_group_id',$id)->exists(),422,'Староста должен состоять в этой группе.');DB::table('study_groups')->where('id',$id)->update(['curator_teacher_id'=>$r->curator_teacher_id,'starosta_student_id'=>$r->starosta_student_id,'updated_at'=>now()]);return back()->with('success','Назначения сохранены');}
}
