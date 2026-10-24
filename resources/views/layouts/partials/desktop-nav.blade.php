@php($u=auth()->user())
@php($psychWorkspace=$u&&$u->hasRole('psychologist')&&!$u->isSuperAdmin()&&!$u->hasRole('admin'))
@php($socialWorkspace=$u&&$u->hasRole('social_teacher')&&!$u->isSuperAdmin()&&!$u->hasRole('admin'))
@if($u)<aside class="desktop-side">
<div class="small text-uppercase px-2 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Рабочее пространство</div>
<nav class="nav flex-column">
<a class="nav-link" href="{{$u->teacher_id?route('teacher.workspace'):route('dashboard')}}">⌂ <span>{{$u->teacher_id?'Кабинет':'Главная'}}</span></a>
<a class="nav-link" href="{{($u->teacher_id||$u->student_id)?route('my.schedule'):route('public.schedule')}}">📅 <span>{{($u->teacher_id||$u->student_id)?'Моё расписание':'Расписание'}}</span></a>
@if(!$psychWorkspace&&!$socialWorkspace&&$u->canDo('schedule.view'))<a class="nav-link" href="{{route('journal.today')}}">▣ <span>Журнал</span></a>@endif
@if($u->teacher_id)
<a class="nav-link" href="{{route('teacher.workspace')}}">👨‍🏫 <span>Кабинет преподавателя</span></a>
<a class="nav-link" href="{{route('curriculum.index')}}#programs">📘 <span>Рабочие программы</span></a>
<a class="nav-link" href="{{route('curriculum.index')}}">🗒 <span>КТП</span></a>
<a class="nav-link" href="{{route('teacher.curator.index')}}">👥 <span>Кураторство</span></a>
@endif
<a class="nav-link" href="{{route('calendar.index')}}">🗓 <span>Календарь</span></a>
<a class="nav-link" href="{{route('requests.index')}}">🎫 <span>Обращения</span></a>
</nav>
@if(!$psychWorkspace&&!$socialWorkspace&&($u->canDo('students.manage')||$u->canDo('reports.view')||$u->isSuperAdmin()))
<div class="small text-uppercase px-2 mt-4 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Учебный процесс</div><nav class="nav flex-column">
@if($u->canDo('students.manage'))<a class="nav-link" href="{{route('academic.index')}}">🏛 <span>Учебная часть</span></a><a class="nav-link" href="{{route('admin.groups.index')}}">👥 <span>Группы</span></a><a class="nav-link" href="{{route('admin.students.index')}}">🎓 <span>Студенты</span></a>@endif
@if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('academic_head')||$u->hasRole('head_academic'))<a class="nav-link" href="{{route('admin.teachers.index')}}">👨‍🏫 <span>Преподаватели</span></a>@endif
@if($u->isSuperAdmin())<a class="nav-link" href="{{route('teacher.workspace')}}">👨‍🏫 <span>Преподаватель</span></a><a class="nav-link" href="{{route('management.index')}}">◫ <span>Управление</span></a>@endif
@if($u->canDo('reports.view'))<a class="nav-link" href="{{route('analytics.index')}}">📈 <span>Аналитика</span></a><a class="nav-link" href="{{route('reports.groups')}}">📊 <span>Отчёты</span></a>@endif
</nav>@endif
<div class="small text-uppercase px-2 mt-4 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Подразделения</div><nav class="nav flex-column">
@if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('social_teacher')||$u->canDo('socialwork.view'))<a class="nav-link" href="{{route('social-teacher.index')}}">🤲 <span>Социальный педагог</span></a>@endif
@if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('psychologist')||$u->canDo('psychology.view'))<a class="nav-link" href="{{route('psychologist.index')}}">🧠 <span>Психолог</span></a>@endif
@if(!$psychWorkspace&&!$socialWorkspace&&($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('upbringing_head')||$u->hasRole('curator')))<a class="nav-link" href="{{route('upbringing.index')}}">🤝 <span>Воспитательная работа</span></a>@endif
@if($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('medical_worker')||$u->hasRole('academic_head'))<a class="nav-link" href="{{route('medical.index')}}">⚕ <span>Медицинский кабинет</span></a>@endif
@if(!$psychWorkspace&&!$socialWorkspace&&($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('academic_head')||$u->canDo('students.manage')))<a class="nav-link" href="{{route('employment.index')}}">💼 <span>Трудоустройство</span></a>@endif
@if($u->isSuperAdmin()||$u->hasRole('admin'))<a class="nav-link" href="{{route('admin.hr.index')}}">👔 <span>Кадры</span></a>@endif
@if($u->canDo('library.view'))<a class="nav-link" href="{{route('library.index')}}">📚 <span>Библиотека</span></a>@endif
@if($u->canDo('dormitory.view'))<a class="nav-link" href="{{route('dormitory.index')}}">🏠 <span>Общежитие</span></a>@endif
@if($u->canDo('practice.view'))<a class="nav-link" href="{{route('practice-office.index')}}">🧰 <span>Практика</span></a>@endif
@if($u->canDo('canteen.view'))<a class="nav-link" href="{{route('canteen.index')}}">🍽 <span>Столовая</span></a>@endif
@if($u->canDo('methodical.view'))<a class="nav-link" href="{{route('methodical.index')}}">📖 <span>Методический отдел</span></a>@endif
@if($u->canDo('quality.view'))<a class="nav-link" href="{{route('quality.index')}}">✓ <span>Качество</span></a>@endif
@if($u->canDo('edo.view'))<a class="nav-link" href="{{route('edo.index')}}">🗂 <span>ЭДО</span></a>@endif
@if($u->canDo('office.view'))<a class="nav-link" href="{{route('office.index')}}">🏢 <span>Канцелярия</span></a>@endif
@if($u->canDo('facility.view'))<a class="nav-link" href="{{route('facility.index')}}">🛠 <span>АХЧ</span></a>@endif
</nav>
@if($u->canDo('users.manage')||$u->canDo('roles.manage')||$u->canDo('schedule.import'))
<div class="small text-uppercase px-2 mt-4 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Система</div><nav class="nav flex-column">
@if($u->canDo('users.manage'))<a class="nav-link" href="{{route('admin.users.index')}}">👤 <span>Пользователи</span></a><a class="nav-link" href="{{route('admin.credentials.index')}}">🔑 <span>Учётные данные</span></a>@endif
@if($u->canDo('roles.manage'))<a class="nav-link" href="{{route('admin.roles.index')}}">🔐 <span>Роли и права</span></a>@endif
@if($u->canDo('schedule.import'))<a class="nav-link" href="{{route('admin.schedule.import')}}">⇄ <span>Импорт Rector XML</span></a>@endif
</nav>@endif
</aside>@endif