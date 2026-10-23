<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class VisibilityController extends Controller{
public function group(Request $r,$id){$this->set($r,'study_groups',$id);return back()->with('success','Видимость группы изменена');}
public function teacher(Request $r,$id){$this->set($r,'teachers',$id);return back()->with('success','Видимость преподавателя изменена');}
public function student(Request $r,$id){$this->set($r,'students',$id);return back()->with('success','Видимость студента изменена');}
private function set(Request $r,string $table,$id){abort_unless($r->user()->isSuperAdmin()||$r->user()->canDo('users.manage')||$r->user()->canDo('students.manage'),403);$r->validate(['visible'=>'required|boolean']);DB::table($table)->where('id',$id)->update(['is_visible'=>$r->boolean('visible'),'updated_at'=>now()]);}
}