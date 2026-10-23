<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,DashboardController,JournalController,AcademicPeriodController,ReportController,SemesterResultController,PublicScheduleController};
use App\Http\Controllers\Admin\{UserController,RoleController,GroupController,StudentController,ScheduleImportController};
Route::get('/schedule',[PublicScheduleController::class,'index'])->name('public.schedule');
Route::get('/',function(){return auth()->check()?redirect()->route('dashboard'):redirect()->route('public.schedule');})->name('home');
Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'form'])->name('login');Route::post('/login',[AuthController::class,'login'])->name('login.store');});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware(['auth','academic.period'])->group(function(){
 Route::post('/period/select',[AcademicPeriodController::class,'select'])->name('period.select');
 Route::post('/period/{period}/close',[AcademicPeriodController::class,'close'])->name('period.close');
 Route::post('/period/{period}/open',[AcademicPeriodController::class,'open'])->name('period.open');
 Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
 Route::get('/reports',[ReportController::class,'groups'])->name('reports.groups')->middleware('permission:reports.view');
 Route::get('/reports/group/{group}',[ReportController::class,'group'])->name('reports.group')->middleware('permission:reports.view');
 Route::get('/reports/group/{group}/csv',[ReportController::class,'csv'])->name('reports.group.csv')->middleware('permission:reports.export');
 Route::get('/reports/student/{student}',[ReportController::class,'student'])->name('reports.student')->middleware('permission:reports.view');
 Route::get('/semester/{group}/{subject}',[SemesterResultController::class,'edit'])->name('semester.edit')->middleware('permission:semester.view');
 Route::post('/semester/{group}/{subject}',[SemesterResultController::class,'save'])->name('semester.save')->middleware('permission:semester.mark');
 Route::get('/journal',[JournalController::class,'today'])->name('journal.today')->middleware('permission:schedule.view');
 Route::get('/journal/lesson/{lesson}',[JournalController::class,'show'])->name('journal.lesson');
 Route::post('/journal/lesson/{lesson}/attendance',[JournalController::class,'attendance'])->name('journal.attendance');
 Route::post('/journal/lesson/{lesson}/attendance/all-present',[JournalController::class,'allPresent'])->name('journal.attendance.all');
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
