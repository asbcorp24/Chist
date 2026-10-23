<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;use Illuminate\Support\Facades\Auth;
class AuthController extends Controller{
public function form(){return view('auth.login');}
public function login(Request $r){$d=$r->validate(['login'=>'required|string','password'=>'required']);$field=filter_var($d['login'],FILTER_VALIDATE_EMAIL)?'email':'login';if(Auth::attempt([$field=>$d['login'],'password'=>$d['password']],$r->boolean('remember'))){if(!$r->user()->is_active){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return back()->withErrors(['login'=>'Учётная запись отключена.'])->onlyInput('login');}$r->session()->regenerate();return redirect()->intended(route('dashboard'));}return back()->withErrors(['login'=>'Неверный логин или пароль'])->onlyInput('login');}
public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('public.schedule');}
}