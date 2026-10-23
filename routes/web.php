<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,DashboardController};
use App\Http\Controllers\Admin\{UserController,RoleController};
Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'form'])->name('login');Route::post('/login',[AuthController::class,'login'])->name('login.store');});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){
 Route::get('/',[DashboardController::class,'index'])->name('dashboard');
 Route::prefix('admin')->name('admin.')->group(function(){
  Route::resource('users',UserController::class)->except(['show','destroy'])->middleware('permission:users.manage');
  Route::get('roles',[RoleController::class,'index'])->name('roles.index')->middleware('permission:roles.manage');
  Route::get('roles/{role}/edit',[RoleController::class,'edit'])->name('roles.edit')->middleware('permission:roles.manage');
  Route::put('roles/{role}',[RoleController::class,'update'])->name('roles.update')->middleware('permission:roles.manage');
 });
});
