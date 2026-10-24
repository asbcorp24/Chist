@php($u = auth()->user())
@if($u)
<div class="mobile-nav d-lg-none">
  <div class="d-flex justify-content-around align-items-center">
    <a class="navitem {{ ($u->teacher_id?request()->routeIs('teacher.workspace'):request()->routeIs('dashboard')) ? 'active' : '' }}" href="{{ $u->teacher_id?route('teacher.workspace'):route('dashboard') }}"><span class="ico">⌂</span><span>{{$u->teacher_id?'Кабинет':'Главная'}}</span></a>
    @if($u->canDo('schedule.view'))
      <a class="navitem {{ request()->routeIs('journal.*') ? 'active' : '' }}" href="{{ route('journal.today') }}"><span class="ico">▣</span><span>Журнал</span></a>
    @else
      <a class="navitem {{ request()->routeIs('public.schedule') ? 'active' : '' }}" href="{{ ($u->teacher_id||$u->student_id) ? route('my.schedule') : route('public.schedule') }}"><span class="ico">📅</span><span>{{($u->teacher_id||$u->student_id)?'Моё расписание':'Расписание'}}</span></a>
    @endif
    @if($u->student_id)
      <a class="navitem quick {{ request()->routeIs('student.progress') ? 'active' : '' }}" href="{{ route('student.progress') }}"><span class="ico">🎓</span><span>Успеваемость</span></a>
    @elseif($u->teacher_id)
      <a class="navitem quick {{ request()->routeIs('teacher.workspace') ? 'active' : '' }}" href="{{ route('teacher.workspace') }}"><span class="ico">✓</span><span>Кабинет</span></a>
    @else
      <a class="navitem quick {{ request()->routeIs('requests.*') ? 'active' : '' }}" href="{{ route('requests.index') }}"><span class="ico">🎫</span><span>Обращения</span></a>
    @endif
    <a class="navitem" data-bs-toggle="offcanvas" href="#moreMenu" role="button" aria-controls="moreMenu"><span class="ico">☰</span><span>Ещё</span></a>
  </div>
</div>

<div class="offcanvas offcanvas-bottom more-sheet" tabindex="-1" id="moreMenu" style="height:auto;max-height:82vh">
 <div class="offcanvas-header"><h5 class="offcanvas-title">Разделы Chist</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
 <div class="offcanvas-body pt-0">
  <div class="row g-2">
    <div class="col-6"><a class="menu-tile" href="{{ ($u->teacher_id||$u->student_id)?route('my.schedule'):route('public.schedule') }}"><span class="ico">📅</span><span>{{($u->teacher_id||$u->student_id)?'Моё расписание':'Расписание'}}</span></a></div>
    <div class="col-6"><a class="menu-tile" href="{{ route('calendar.index') }}"><span class="ico">🗓</span><span>Календарь</span></a></div>
    <div class="col-6"><a class="menu-tile" href="{{ route('announcements.index') }}"><span class="ico">🔔</span><span>Объявления</span></a></div>
    <div class="col-6"><a class="menu-tile" href="{{ route('requests.index') }}"><span class="ico">🎫</span><span>Обращения</span></a></div>
    @if($u->student_id)
      <div class="col-6"><a class="menu-tile" href="{{ route('student.progress') }}"><span class="ico">📊</span><span>Успеваемость</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('student.life') }}"><span class="ico">🎓</span><span>Студент</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('practice-student.index') }}"><span class="ico">🧰</span><span>Практика</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('library.my') }}"><span class="ico">📚</span><span>Мои книги</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('canteen.student') }}"><span class="ico">🍽</span><span>Питание</span></a></div>
    @endif
    @if($u->teacher_id)
      <div class="col-6"><a class="menu-tile" href="{{ route('teacher.workspace') }}"><span class="ico">👨‍🏫</span><span>Кабинет</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('curriculum.index') }}#programs"><span class="ico">📘</span><span>Рабочие программы</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('curriculum.index') }}"><span class="ico">🗒</span><span>КТП</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('teacher.curator.index') }}"><span class="ico">👥</span><span>Кураторство</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('homework.teacher') }}"><span class="ico">✓</span><span>Домашние задания</span></a></div>
    @endif
    @if($u->canDo('reports.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('analytics.index') }}"><span class="ico">📈</span><span>Аналитика</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('documents.index') }}"><span class="ico">📄</span><span>Документы</span></a></div>
    @endif
    @if($u->canDo('students.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('admin.groups.index') }}"><span class="ico">👥</span><span>Группы</span></a></div>
    @endif
    @if($u->canDo('students.manage'))
      <div class="col-6"><a class="menu-tile" href="{{ route('admin.students.index') }}"><span class="ico">🎓</span><span>Студенты</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('academic.index') }}"><span class="ico">🏛</span><span>Учебная часть</span></a></div>
    @endif
    @if($u->canDo('users.manage'))
      <div class="col-6"><a class="menu-tile" href="{{ route('admin.users.index') }}"><span class="ico">👤</span><span>Пользователи</span></a></div>
      <div class="col-6"><a class="menu-tile" href="{{ route('admin.credentials.index') }}"><span class="ico">🔑</span><span>Логины и пароли</span></a></div>
    @endif
    @if($u->canDo('roles.manage'))
      <div class="col-6"><a class="menu-tile" href="{{ route('admin.roles.index') }}"><span class="ico">🔐</span><span>Роли и права</span></a></div>
    @endif
    @if($u->isSuperAdmin() || $u->hasRole('admin') || $u->hasRole('academic_head'))
      <div class="col-6"><a class="menu-tile" href="{{ route('management.index') }}"><span class="ico">🏛</span><span>Руководитель</span></a></div>
    @endif
    @if($u->isSuperAdmin() || $u->hasRole('admin') || $u->hasRole('upbringing_head') || $u->hasRole('curator'))
      <div class="col-6"><a class="menu-tile" href="{{route('upbringing.index')}}"><span class="ico">🤝</span><span>Воспитательная</span></a></div>
    @endif
    @if($u->isSuperAdmin() || $u->hasRole('admin') || $u->hasRole('medical_worker') || $u->hasRole('academic_head'))
      <div class="col-6"><a class="menu-tile" href="{{route('medical.index')}}"><span class="ico">⚕</span><span>Медкабинет</span></a></div>
    @endif
    @if($u->isSuperAdmin() || $u->hasRole('admin') || $u->hasRole('academic_head') || $u->canDo('students.manage'))
      <div class="col-6"><a class="menu-tile" href="{{route('employment.index')}}"><span class="ico">💼</span><span>Трудоустройство</span></a></div>
    @endif
    @if($u->isSuperAdmin() || $u->hasRole('admin'))
      <div class="col-6"><a class="menu-tile" href="{{route('admin.hr.index')}}"><span class="ico">👔</span><span>Кадры</span></a></div>
    @endif
    @if($u->canDo('practice.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('practice-office.index') }}"><span class="ico">🧰</span><span>Практика</span></a></div>
    @endif
    @if($u->canDo('library.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('library.index') }}"><span class="ico">📚</span><span>Библиотека</span></a></div>
    @endif
    @if($u->canDo('dormitory.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('dormitory.index') }}"><span class="ico">🏠</span><span>Общежитие</span></a></div>
    @endif
    @if($u->canDo('canteen.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('canteen.index') }}"><span class="ico">🍽</span><span>Столовая</span></a></div>
    @endif
    @if($u->canDo('facility.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('facility.index') }}"><span class="ico">🛠</span><span>АХЧ</span></a></div>
    @endif
    @if($u->canDo('quality.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('quality.index') }}"><span class="ico">📈</span><span>Качество</span></a></div>
    @endif
    @if($u->canDo('methodical.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('methodical.index') }}"><span class="ico">📚</span><span>Методический</span></a></div>
    @endif
    @if($u->canDo('edo.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('edo.index') }}"><span class="ico">🗂</span><span>ЭДО</span></a></div>
    @endif
    @if($u->canDo('office.view'))
      <div class="col-6"><a class="menu-tile" href="{{ route('office.index') }}"><span class="ico">🏢</span><span>Канцелярия</span></a></div>
    @endif
  </div>
  <form method="post" action="{{ route('logout') }}" class="mt-3">@csrf<button class="btn btn-outline-danger w-100" type="submit">Выйти</button></form>
 </div>
</div>
@endif
