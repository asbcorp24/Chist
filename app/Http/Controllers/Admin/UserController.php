<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\{User,Role}; use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash;
class UserController extends Controller {
 public function index(){return view('admin.users.index',['users'=>User::with('roles')->orderBy('name')->paginate(30)]);}
 public function create(){return view('admin.users.form',['user'=>new User,'roles'=>$this->assignableRoles()]);}
 public function store(Request $r){$d=$r->validate(['name'=>'required|max:255','email'=>'required|email|unique:users','password'=>'required|min:8','roles'=>'array']);$u=User::create(['name'=>$d['name'],'email'=>$d['email'],'password'=>Hash::make($d['password']),'is_active'=>true]);$this->syncRoles($r,$u);return redirect()->route('admin.users.index')->with('success','Пользователь создан');}
 public function edit(User $user){$this->protectSuper($user);return view('admin.users.form',compact('user')+['roles'=>$this->assignableRoles()]);}
 public function update(Request $r,User $user){$this->protectSuper($user);$d=$r->validate(['name'=>'required|max:255','email'=>'required|email|unique:users,email,'.$user->id,'password'=>'nullable|min:8','roles'=>'array','is_active'=>'nullable|boolean']);$u=['name'=>$d['name'],'email'=>$d['email'],'is_active'=>$r->boolean('is_active')];if(!empty($d['password']))$u['password']=Hash::make($d['password']);$user->update($u);$this->syncRoles($r,$user);return back()->with('success','Изменения сохранены');}
 private function protectSuper(User $target){if($target->isSuperAdmin()&&!auth()->user()->isSuperAdmin())abort(403);}
 private function assignableRoles(){return auth()->user()->isSuperAdmin()?Role::orderByDesc('level')->get():Role::where('code','!=','super_admin')->orderByDesc('level')->get();}
 private function syncRoles(Request $r,User $u){$ids=Role::whereIn('id',$r->input('roles',[]));if(!auth()->user()->isSuperAdmin())$ids->where('code','!=','super_admin');$u->roles()->sync($ids->pluck('id'));}
}
