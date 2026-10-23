@php($u = auth()->user())
@if($u)
<aside class="desktop-side p-3"><div class="nav flex-column nav-pills gap-1">
<a class="nav-link" href="{{ route('dashboard') }}">⌂ Главная</a>
<a class="nav-link" href="{{ route('public.schedule') }}">📅 Расписание</a>
@if($u->canDo('schedule.view'))<a class="nav-link" href="{{ route('journal.today') }}">▣ Журнал</a>@endif
<a class="nav-link" href="{{ route('calendar.index') }}">🗓 Календарь</a>
<a class="nav-link" href="{{ route('requests.index') }}">🎫 Обращения</a>
@if($u->canDo('reports.view'))<a class="nav-link" href="{{ route('analytics.index') }}">📈 Аналитика</a><a class="nav-link" href="{{ route('reports.groups') }}">📊 Отчёты</a>@endif
@if($u->canDo('students.manage'))<a class="nav-link" href="{{ route('academic.index') }}">🏛 Учебная часть</a><a class="nav-link" href="{{ route('admin.groups.index') }}">👥 Группы</a><a class="nav-link" href="{{ route('admin.students.index') }}">🎓 Студенты</a>@endif
@if($u->isSuperAdmin())<a class="nav-link" href="{{ route('teacher.workspace') }}">👨‍🏫 Преподаватель</a><a class="nav-link" href="{{ route('management.index') }}">🏛 Управление</a>@endif
@if($u->canDo('library.view'))<a class="nav-link" href="{{ route('library.index') }}">📚 Библиотека</a>@endif
@if($u->canDo('dormitory.view'))<a class="nav-link" href="{{ route('dormitory.index') }}">🏠 Общежитие</a>@endif
@if($u->canDo('practice.view'))<a class="nav-link" href="{{ route('practice-office.index') }}">🧰 Практика</a>@endif
@if($u->canDo('canteen.view'))<a class="nav-link" href="{{ route('canteen.index') }}">🍽 Столовая</a>@endif
@if($u->canDo('methodical.view'))<a class="nav-link" href="{{ route('methodical.index') }}">📚 Методический отдел</a>@endif
@if($u->canDo('quality.view'))<a class="nav-link" href="{{ route('quality.index') }}">📈 Качество</a>@endif
@if($u->canDo('edo.view'))<a class="nav-link" href="{{ route('edo.index') }}">🗂 ЭДО</a>@endif
@if($u->canDo('office.view'))<a class="nav-link" href="{{ route('office.index') }}">🏢 Канцелярия</a>@endif
@if($u->canDo('facility.view'))<a class="nav-link" href="{{ route('facility.index') }}">🛠 АХЧ</a>@endif
@if($u->canDo('users.manage'))<a class="nav-link" href="{{ route('admin.users.index') }}">👤 Пользователи</a><a class="nav-link" href="{{ route('admin.credentials.index') }}">🔑 Учётные данные</a>@endif
@if($u->canDo('roles.manage'))<a class="nav-link" href="{{ route('admin.roles.index') }}">🔐 Роли и права</a>@endif
@if($u->canDo('schedule.import'))<a class="nav-link" href="{{ route('admin.schedule.import') }}">⇄ Импорт Rector XML</a>@endif
</div></aside>
@endif