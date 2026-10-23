<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\{Role,Permission}; use Illuminate\Http\Request;
class RoleController extends Controller {
 public function index(){return view('admin.roles.index',['roles'=>Role::withCount(['users','permissions'])->orderByDesc('level')->get()]);}
 public function edit(Role $role){if($role->code==='super_admin'&&!auth()->user()->isSuperAdmin())abort(403);return view('admin.roles.form',['role'=>$role,'permissions'=>Permission::orderBy('group')->orderBy('name')->get()->groupBy('group')]);}
 public function update(Request $r,Role $role){if($role->code==='super_admin')abort_unless(auth()->user()->isSuperAdmin(),403);$r->validate(['name'=>'required|max:255','permissions'=>'array']);if(!$role->is_system)$role->update(['name'=>$r->name]);if($role->code!=='super_admin')$role->permissions()->sync(Permission::whereIn('id',$r->input('permissions',[]))->pluck('id'));return back()->with('success','Права роли сохранены');}
}
