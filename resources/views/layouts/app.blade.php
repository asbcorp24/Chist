<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#172033"><meta name="csrf-token" content="{{csrf_token()}}"><link rel="manifest" href="/manifest.webmanifest"><title>@yield('title','Chist')</title><link href="/vendor/bootstrap/bootstrap.min.css" rel="stylesheet"><style>
:root{--app:#172033;--accent:#0d6efd}body{background:#f4f6f9;padding-bottom:82px}.card{border:0;border-radius:18px;box-shadow:0 2px 12px #0000000d}.topbar{background:var(--app)}.desktop-side{width:250px}.app-shell{max-width:1200px}.mobile-nav{position:fixed;z-index:1040;left:0;right:0;bottom:0;padding-bottom:env(safe-area-inset-bottom);background:#fff;border-top:1px solid #e7e9ed;box-shadow:0 -4px 20px #00000012}.mobile-nav .navitem{height:66px;min-width:58px;color:#6c757d;text-decoration:none;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:.72rem;gap:2px}.mobile-nav .navitem.active{color:var(--accent);font-weight:700}.mobile-nav .ico{font-size:1.35rem;line-height:1}.quick{transform:translateY(-12px);width:54px;height:54px;border-radius:50%;background:var(--accent);color:#fff!important;box-shadow:0 5px 16px #0d6efd55}.more-sheet{border-radius:24px 24px 0 0}.menu-tile{min-height:78px;border-radius:16px;background:#f5f7fa;text-decoration:none;color:#212529;display:flex;align-items:center;padding:14px;gap:12px}.menu-tile .ico{font-size:1.45rem}.period-card select{border:0;background:#f7f8fa;font-weight:600}@media(min-width:992px){body{padding-bottom:0}.mobile-nav{display:none!important}.desktop-layout{display:flex}.desktop-side{display:block!important}.app-main{flex:1;min-width:0}}@media(max-width:991.98px){.desktop-side{display:none}.top-user{max-width:45vw;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.btn{min-height:44px}.student-row .btn{min-width:40px}}
</style><link rel="stylesheet" href="/css/chist-analytics.css"></head><body><nav class="navbar navbar-dark topbar sticky-top"><div class="container-fluid app-shell"><a class="navbar-brand fw-bold" href="{{route('dashboard')}}">Chist</a><div class="ms-auto d-flex align-items-center gap-2"><span id="netState" class="badge text-bg-success">Онлайн</span><span id="queueState" class="badge text-bg-warning d-none"></span><div class="text-white small top-user">@if(auth()->check()){{auth()->user()->name}}@endif</div></div></div></nav>

@if(auth()->check())
<div class="desktop-layout app-shell mx-auto">
    <aside class="desktop-side p-3">
        <div class="nav flex-column nav-pills gap-1">
            <a class="nav-link" href="{{ route('dashboard') }}">⌂ Главная</a>
            @if(auth()->user()->canDo('schedule.view'))
                <a class="nav-link" href="{{ route('journal.today') }}">▣ Журнал</a>
            @endif
            <a class="nav-link" href="{{ route('public.schedule') }}">📅 Расписание</a>
        </div>
    </aside>
@endif

<main class="container-fluid app-main py-3" style="max-width:950px">
    {{session('success')}}</div>@endif@if($errors->any())<div class="alert alert-danger">{{$errors->first()}}</div>@endif @yield('content')
</main>

@if(auth()->check())
</div>
<div class="mobile-nav d-lg-none">
    <div class="d-flex justify-content-around">
        <a class="navitem" href="{{ route('dashboard') }}"><span class="ico">⌂</span><span>Главная</span></a>
        <a class="navitem" href="{{ route('public.schedule') }}"><span class="ico">📅</span><span>Расписание</span></a>
        <form method="post" action="{{ route('logout') }}" class="m-0">@csrf<button class="navitem border-0 bg-transparent" type="submit"><span class="ico">↪</span><span>Выйти</span></button></form>
    </div>
</div>
@endif
<script src="/vendor/jquery/jquery.min.js"></script><script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script><script>if('serviceWorker'in navigator){window.addEventListener('load',()=>navigator.serviceWorker.register('/sw.js').catch(()=>{}));}function net(){let e=document.getElementById('netState');if(!e)return;e.textContent=navigator.onLine?'Онлайн':'Офлайн';e.className='badge '+(navigator.onLine?'text-bg-success':'text-bg-danger')}function qs(n){let e=document.getElementById('queueState');if(!e)return;e.textContent=n?'⏳ '+n:'';e.classList.toggle('d-none',!n)}window.addEventListener('online',net);window.addEventListener('offline',net);window.addEventListener('chist-queue',e=>qs(e.detail));net();try{qs(JSON.parse(localStorage.getItem('chist-attendance-queue')||'[]').length)}catch(e){}</script><script src="/js/chist-charts.js"></script>@stack('scripts')</body></html>