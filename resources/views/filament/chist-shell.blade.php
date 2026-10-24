@php($currentUser=auth()->user())
<div class="ch-filament-shell">
<header class="navbar ch-topbar sticky-top"><div class="container-fluid px-3 px-lg-4">
<a class="ch-brand" href="{{$currentUser&&$currentUser->teacher_id?route('teacher.workspace'):route('dashboard')}}"><span class="ch-logo">C</span><span><b>Chist</b><small>Колледж · единая система</small></span></a>
<div class="ms-auto ch-user">@if($currentUser)<div class="dropdown"><button class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown"><div class="ch-avatar">{{mb_strtoupper(mb_substr($currentUser->name??'U',0,1))}}</div><span class="ch-user-name fw-semibold">{{$currentUser->name}}</span></button><div class="dropdown-menu dropdown-menu-end shadow"><a class="dropdown-item" href="{{route('profile.edit')}}">Профиль и безопасность</a><div class="dropdown-divider"></div><form method="post" action="{{route('logout')}}">@csrf<button class="dropdown-item text-danger">Выйти из системы</button></form></div></div>@endif</div>
</div></header>
<div class="ch-filament-side">@include('layouts.partials.desktop-nav')</div>
</div>