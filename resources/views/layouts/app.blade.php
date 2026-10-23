<!doctype html>
<html lang="ru"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#ffffff"><meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.webmanifest"><title>@yield('title','Chist')</title>
<link href="/vendor/bootstrap/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/css/chist-analytics.css"><link rel="stylesheet" href="/css/chist-ui.css">
</head><body>
@php($currentUser=auth()->user())
<header class="navbar ch-topbar sticky-top"><div class="container-fluid px-3 px-lg-4">
<a class="ch-brand" href="{{route('dashboard')}}"><span class="ch-logo">C</span><span><b>Chist</b><small>Колледж · единая система</small></span></a>
<div class="ms-auto ch-user"><span id="netState" class="badge bg-success-subtle text-success ch-net">Онлайн</span>
@if($currentUser)<div class="ch-avatar">{{mb_strtoupper(mb_substr($currentUser->name??'U',0,1))}}</div><span class="ch-user-name fw-semibold">{{$currentUser->name}}</span>@endif
</div></div></header>
<div class="ch-shell">
@if($currentUser) @include('layouts.partials.desktop-nav') @endif
<main class="ch-main"><div class="ch-content">
@if(session('success'))<div class="alert alert-success shadow-sm">{{session('success')}}</div>@endif
@if($errors->any())<div class="alert alert-danger shadow-sm">{{$errors->first()}}</div>@endif
@yield('content')
</div></main>
</div>
@if($currentUser) @include('layouts.partials.mobile-nav') @endif
<script src="/vendor/jquery/jquery.min.js"></script><script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script><script src="/js/chist-charts.js"></script><script src="/js/chist-responsive.js"></script><script src="/js/chist-responsive.js"></script>
<script>
if('serviceWorker'in navigator)window.addEventListener('load',()=>navigator.serviceWorker.register('/sw.js').catch(()=>{}));
function net(){const e=document.getElementById('netState');if(!e)return;const on=navigator.onLine;e.textContent=on?'Онлайн':'Офлайн';e.className='badge ch-net '+(on?'bg-success-subtle text-success':'bg-danger-subtle text-danger')}addEventListener('online',net);addEventListener('offline',net);net();
document.querySelectorAll('.desktop-side .nav-link').forEach(a=>{try{if(a.pathname===location.pathname||location.pathname.startsWith(a.pathname+'/'))a.classList.add('active')}catch(e){}});
</script>@stack('scripts')</body></html>