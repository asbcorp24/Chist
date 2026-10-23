<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
 public function form(){return view('auth.login');}
 public function login(Request $r){$c=$r->validate(['email'=>'required|email','password'=>'required']);if(Auth::attempt($c,(bool)$r->boolean('remember'))){if(!$r->user()->is_active){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return back()->withErrors(['email'=>'Учётная запись отключена.'])->onlyInput('email');}$r->session()->regenerate();return redirect()->intended(route('dashboard'));}return back()->withErrors(['email'=>'Неверный логин или пароль'])->onlyInput('email');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('public.schedule');}
}
