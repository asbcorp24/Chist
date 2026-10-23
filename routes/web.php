<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,DashboardController,JournalController};
use App\Http\Controllers\Admin\{UserController,RoleController,GroupController,StudentController,ScheduleImportController};
Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'form'])->name('login');Route::post('/login',[AuthController::class,'login'])->name('login.store');});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){
 Route::get('/',[DashboardController::class,'index'])->name('dashboard');
 Route::get('/journal',[JournalController::class,'today'])->name('journal.today')->middleware('permission:schedule.view');
 Route::get('/journal/lesson/{lesson}',[JournalController::class,'show'])->name('journal.lesson');
 Route::post('/journal/lesson/{lesson}/attendance',[JournalController::class,'attendance'])->name('journal.attendance');
 Route::post('/journal/lesson/{lesson}/grade',[JournalController::class,'grade'])->name('journal.grade');
 Route::prefix('admin')->name('admin.')->group(function(){
  Route::get('schedule/import',[ScheduleImportController::class,'index'])->name('schedule.import')->middleware('permission:schedule.import');
  Route::post('schedule/import',[ScheduleImportController::class,'store'])->name('schedule.import.store')->middleware('permission:schedule.import');
  Route::resource('users',UserController::class)->except(['show','destroy'])->middleware('permission:users.manage');
  Route::get('roles',[RoleController::class,'index'])->name('roles.index')->middleware('permission:roles.manage');
  Route::get('roles/{role}/edit',[RoleController::class,'edit'])->name('roles.edit')->middleware('permission:roles.manage');
  Route::put('roles/{role}',[RoleController::class,'update'])->name('roles.update')->middleware('permission:roles.manage');
  Route::get('groups',[GroupController::class,'index'])->name('groups.index')->middleware('permission:students.view');
  Route::get('groups/{group}/edit',[GroupController::class,'edit'])->name('groups.edit')->middleware('permission:students.manage');
  Route::put('groups/{group}',[GroupController::class,'update'])->name('groups.update')->middleware('permission:students.manage');
  Route::resource('students',StudentController::class)->except(['show','destroy'])->middleware('permission:students.manage');
 });
});
