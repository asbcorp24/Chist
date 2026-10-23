<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder {
 public function run(){
  $roles=[
   'super_admin'=>['Супер-администратор',100],
   'admin'=>['Администратор',80],
   'curator'=>['Классный руководитель',50],
   'teacher'=>['Преподаватель',40],
   'starosta'=>['Староста',20],
  ];
  foreach($roles as $code=>$r)DB::table('roles')->updateOrInsert(['code'=>$code],['name'=>$r[0],'level'=>$r[1],'is_system'=>1,'created_at'=>now(),'updated_at'=>now()]);
  $p=[
   'users.view'=>['Просмотр пользователей','Пользователи'],'users.manage'=>['Управление пользователями','Пользователи'],
   'roles.manage'=>['Управление ролями и правами','Пользователи'],'superadmins.manage'=>['Управление супер-администраторами','Пользователи'],
   'periods.manage'=>['Учебные годы и периоды','Учебный процесс'],'periods.close'=>['Закрытие/открытие периодов','Учебный процесс'],
   'schedule.view'=>['Просмотр расписания','Расписание'],'schedule.import'=>['Импорт расписания','Расписание'],'schedule.manage'=>['Редактирование расписания','Расписание'],
   'students.view'=>['Просмотр студентов','Студенты'],'students.manage'=>['Управление студентами','Студенты'],
   'attendance.view'=>['Просмотр посещаемости','Журнал'],'attendance.mark'=>['Ведение посещаемости','Журнал'],
   'grades.view'=>['Просмотр оценок','Журнал'],'grades.mark'=>['Выставление оценок','Журнал'],
   'semester.view'=>['Просмотр семестровых результатов','Журнал'],'semester.mark'=>['Выставление семестровых результатов','Журнал'],
   'reports.view'=>['Просмотр отчётов','Отчёты'],'reports.export'=>['Экспорт отчётов','Отчёты'],
   'audit.view'=>['Просмотр аудита','Система'],'settings.manage'=>['Системные настройки','Система']
  ];
  foreach($p as $code=>$x)DB::table('permissions')->updateOrInsert(['code'=>$code],['name'=>$x[0],'group'=>$x[1],'created_at'=>now(),'updated_at'=>now()]);
  $map=[
   'admin'=>['users.view','users.manage','periods.manage','schedule.view','schedule.import','schedule.manage','students.view','students.manage','attendance.view','attendance.mark','grades.view','grades.mark','semester.view','semester.mark','reports.view','reports.export','audit.view'],
   'curator'=>['schedule.view','students.view','students.manage','attendance.view','grades.view','semester.view','reports.view','reports.export'],
   'teacher'=>['schedule.view','students.view','attendance.view','attendance.mark','grades.view','grades.mark','semester.view','semester.mark','reports.view'],
   'starosta'=>['schedule.view','students.view','attendance.view','attendance.mark']
  ];
  foreach($map as $roleCode=>$codes){$role=DB::table('roles')->where('code',$roleCode)->value('id');foreach($codes as $code){$pid=DB::table('permissions')->where('code',$code)->value('id');DB::table('permission_role')->updateOrInsert(['role_id'=>$role,'permission_id'=>$pid]);}}
 }
}
