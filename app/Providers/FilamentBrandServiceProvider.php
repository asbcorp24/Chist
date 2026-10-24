<?php
namespace App\Providers;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;
class FilamentBrandServiceProvider extends ServiceProvider{
public function boot():void{
Filament::serving(function(){
Filament::registerStyles(['chist-filament-theme'=>'/css/filament-chist.css']);
$u=auth()->user();if(!$u)return;
$items=[];
$add=function(string $label,string $url,string $icon,string $group,int $sort=10)use(&$items){$items[]=NavigationItem::make($label)->url($url)->icon($icon)->group($group)->sort($sort);};
$add($u->teacher_id?'Кабинет':'Главная',$u->teacher_id?route('teacher.workspace'):route('dashboard'),'heroicon-o-home','Рабочее пространство',1);
$add(($u->teacher_id||$u->student_id)?'Моё расписание':'Расписание',($u->teacher_id||$u->student_id)?route('my.schedule'):route('public.schedule'),'heroicon-o-calendar','Рабочее пространство',2);
if($u->teacher_id){$add('Кабинет преподавателя',route('teacher.workspace'),'heroicon-o-academic-cap','Рабочее пространство',3);$add('Рабочие программы',route('curriculum.index').'#programs','heroicon-o-book-open','Рабочее пространство',4);$add('КТП',route('curriculum.index'),'heroicon-o-clipboard-list','Рабочее пространство',5);$add('Кураторство',route('teacher.curator.index'),'heroicon-o-user-group','Рабочее пространство',6);}
$add('Календарь',route('calendar.index'),'heroicon-o-calendar','Рабочее пространство',7);$add('Обращения',route('requests.index'),'heroicon-o-ticket','Рабочее пространство',8);
$psych=$u->hasRole('psychologist')&&!$u->isSuperAdmin()&&!$u->hasRole('admin');$social=$u->hasRole('social_teacher')&&!$u->isSuperAdmin()&&!$u->hasRole('admin');
if(!$psych&&!$social&&$u->canDo('schedule.view'))$add('Журнал',route('journal.today'),'heroicon-o-table','Учебный процесс',1);
if(!$psych&&!$social&&($u->canDo('students.manage')||$u->canDo('reports.view')||$u->isSuperAdmin())){if($u->canDo('students.manage')){$add('Учебная часть',route('academic.index'),'heroicon-o-office-building','Учебный процесс',2);$add('Группы',route('admin.groups.index'),'heroicon-o-user-group','Учебный процесс',3);$add('Студенты',route('admin.students.index'),'heroicon-o-academic-cap','Учебный процесс',4);}if($u->canDo('reports.view')){$add('Аналитика',route('analytics.index'),'heroicon-o-chart-bar','Учебный процесс',7);$add('Отчёты',route('reports.groups'),'heroicon-o-document-report','Учебный процесс',8);}}
if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('head_academic'))$add('Преподаватели',route('admin.teachers.index'),'heroicon-o-users','Учебный процесс',5);
if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('social_teacher')||$u->canDo('socialwork.view'))$add('Социальный педагог',route('social-teacher.index'),'heroicon-o-heart','Подразделения',1);
if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('psychologist')||$u->canDo('psychology.view'))$add('Психолог',route('psychologist.index'),'heroicon-o-light-bulb','Подразделения',2);
if(!$psych&&!$social&&($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('upbringing_head')||$u->hasRole('curator')))$add('Воспитательная работа',route('upbringing.index'),'heroicon-o-heart','Подразделения',3);
if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('medical_worker')||$u->hasRole('academic_head'))$add('Медицинский кабинет',route('medical.index'),'heroicon-o-plus-circle','Подразделения',4);
if(!$psych&&!$social&&($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('academic_head')||$u->canDo('students.manage')))$add('Трудоустройство',route('employment.index'),'heroicon-o-briefcase','Подразделения',5);
if($u->isSuperAdmin()||$u->hasRole('admin'))$add('Кадры',route('admin.hr.index'),'heroicon-o-identification','Подразделения',6);
if($u->canDo('library.view'))$add('Библиотека',route('library.index'),'heroicon-o-book-open','Подразделения',10);
if($u->canDo('dormitory.view'))$add('Общежитие',route('dormitory.index'),'heroicon-o-home','Подразделения',12);
if($u->canDo('canteen.view'))$add('Столовая',route('canteen.index'),'heroicon-o-shopping-bag','Подразделения',13);
if($u->canDo('methodical.view'))$add('Методический отдел',route('methodical.index'),'heroicon-o-book-open','Подразделения',14);
if($u->canDo('quality.view'))$add('Качество',route('quality.index'),'heroicon-o-check-circle','Подразделения',15);
if($u->canDo('edo.view'))$add('ЭДО',route('edo.index'),'heroicon-o-folder','Подразделения',16);
if($u->canDo('office.view'))$add('Канцелярия',route('office.index'),'heroicon-o-office-building','Подразделения',17);
if($u->canDo('facility.view'))$add('АХЧ',route('facility.index'),'heroicon-o-cog','Подразделения',18);
if($u->canDo('practice.view'))$add('Практика',route('practice-office.index'),'heroicon-o-briefcase','Подразделения',11);
if($u->canDo('users.manage')){$add('Пользователи',route('admin.users.index'),'heroicon-o-user','Система',1);$add('Учётные данные',route('admin.credentials.index'),'heroicon-o-key','Система',2);}
if($u->canDo('roles.manage'))$add('Роли и права',route('admin.roles.index'),'heroicon-o-lock-closed','Система',3);if($u->canDo('schedule.import'))$add('Импорт Rector XML',route('admin.schedule.import'),'heroicon-o-refresh','Система',4);
Filament::registerNavigationItems($items);
});
}}
