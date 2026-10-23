@php($u=auth()->user())
@if($u)<aside class="desktop-side">
<div class="small text-uppercase px-2 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Рабочее пространство</div>
<nav class="nav flex-column">
<a class="nav-link" href="{{route('dashboard')}}">⌂ <span>Главная</span></a>
<a class="nav-link" href="{{route('public.schedule')}}">📅 <span>Расписание</span></a>
@if($u->canDo('schedule.view'))<a class="nav-link" href="{{route('journal.today')}}">▣ <span>Журнал</span></a>@endif
<a class="nav-link" href="{{route('calendar.index')}}">🗓 <span>Календарь</span></a>
<a class="nav-link" href="{{route('requests.index')}}">🎫 <span>Обращения</span></a>
</nav>
@if($u->canDo('students.manage')||$u->canDo('reports.view')||$u->isSuperAdmin())
<div class="small text-uppercase px-2 mt-4 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Учебный процесс</div><nav class="nav flex-column">
@if($u->canDo('students.manage'))<a class="nav-link" href="{{route('academic.index')}}">🏛 <span>Учебная часть</span></a><a class="nav-link" href="{{route('admin.groups.index')}}">👥 <span>Группы</span></a><a class="nav-link" href="{{route('admin.students.index')}}">🎓 <span>Студенты</span></a>@endif
@if($u->isSuperAdmin())<a class="nav-link" href="{{route('teacher.workspace')}}">👨‍🏫 <span>Преподаватель</span></a><a class="nav-link" href="{{route('management.index')}}">◫ <span>Управление</span></a>@endif
@if($u->canDo('reports.view'))<a class="nav-link" href="{{route('analytics.index')}}">📈 <span>Аналитика</span></a><a class="nav-link" href="{{route('reports.groups')}}">📊 <span>Отчёты</span></a>@endif
</nav>@endif
<div class="small text-uppercase px-2 mt-4 mb-2" style="color:#667085;font-size:10px;letter-spacing:.08em">Подразделения</div><nav class="nav flex-column">
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