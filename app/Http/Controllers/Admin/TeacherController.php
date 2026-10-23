<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class TeacherController extends Controller{
 public function index(Request $r){$q=DB::table('teachers');if($r->filled('q')){$s='%'.$r->q.'%';$q->where(fn($x)=>$x->where('last_name','like',$s)->orWhere('first_name','like',$s)->orWhere('middle_name','like',$s));}if($r->status==='active')$q->where('active',1);if($r->status==='archive')$q->where('active',0);return view('admin.teachers.index',['teachers'=>$q->orderBy('last_name')->orderBy('first_name')->paginate(50)->withQueryString()]);}
 public function create(){return view('admin.teachers.form',['teacher'=>null]);}
 public function store(Request $r){$d=$this->validated($r);$id=DB::table('teachers')->insertGetId($d+['active'=>1,'created_at'=>now(),'updated_at'=>now()]);return redirect()->route('admin.teachers.edit',$id)->with('success','Преподаватель добавлен');}
 public function edit($id){$t=DB::table('teachers')->find($id);abort_if(!$t,404);return view('admin.teachers.form',['teacher'=>$t]);}
 public function update(Request $r,$id){abort_unless(DB::table('teachers')->where('id',$id)->exists(),404);DB::table('teachers')->where('id',$id)->update($this->validated($r)+['updated_at'=>now()]);return back()->with('success','Карточка преподавателя сохранена');}
 public function destroy($id){abort_unless(DB::table('teachers')->where('id',$id)->exists(),404);DB::table('teachers')->where('id',$id)->update(['active'=>0,'updated_at'=>now()]);return redirect()->route('admin.teachers.index')->with('success','Преподаватель переведён в архив');}
 private function validated(Request $r){return $r->validate(['last_name'=>'required|string|max:100','first_name'=>'required|string|max:100','middle_name'=>'nullable|string|max:100']);}
}