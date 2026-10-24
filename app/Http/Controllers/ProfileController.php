<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller{
 public function edit(Request $r){return view('profile.edit',['user'=>$r->user()]);}
 public function password(Request $r){
  $d=$r->validate(['current_password'=>'required','password'=>'required|string|min:8|confirmed']);
  abort_unless(Hash::check($d['current_password'],$r->user()->password),422,'Текущий пароль указан неверно.');
  $r->user()->update(['password'=>Hash::make($d['password']),'password_encrypted'=>null,'password_changed_at'=>now(),'must_change_password'=>false]);
  return back()->with('success','Пароль изменён.');
 }
}