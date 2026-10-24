<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class TeacherCuratorController extends Controller{
 private function group(Request $r,$id){$u=$r->user();$g=DB::table('study_groups')->find($id);abort_if(!$g,404);abort_unless($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('academic_head')||($u->teacher_id&&$g->curator_teacher_id==$u->teacher_id),403);return $g;}
 public function index(Request $r){$u=$r->user();abort_unless($u->teacher_id||$u->isSuperAdmin()||$u->hasRole('admin'),403);$q=DB::table('study_groups')->where('active',1);if($u->teacher_id&&!($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('academic_head')))$q->where('curator_teacher_id',$u->teacher_id);$groups=$q->orderBy('name')->get();return view('teacher.curator-index',compact('groups'));}
 public function show(Request $r,$id){$g=$this->group($r,$id);$students=DB::table('students')->where('current_group_id',$id)->where('status','active')->orderBy('last_name')->orderBy('first_name')->get();$studentIds=$students->pluck('id');$orders=DB::table('academic_orders')->whereIn('student_id',$studentIds)->orderByDesc('order_date')->orderByDesc('id')->limit(20)->get();return view('teacher.curator-show',compact('g','students','orders'));}
}