<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="theme-color" content="#172033">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>@yield('title', 'Chist')</title>
    <link href="/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/chist-analytics.css">
    <style>
        :root{--app:#172033;--accent:#0d6efd}
        body{background:#f4f6f9;padding-bottom:82px}
        .card{border:0;border-radius:18px;box-shadow:0 2px 12px #0000000d}
        .topbar{background:var(--app)}
        .desktop-side{width:250px}
        .app-shell{max-width:1200px}
        .mobile-nav{position:fixed;z-index:1040;left:0;right:0;bottom:0;padding-bottom:env(safe-area-inset-bottom);background:#fff;border-top:1px solid #e7e9ed;box-shadow:0 -4px 20px #00000012}
        .mobile-nav .navitem{height:66px;min-width:58px;color:#6c757d;text-decoration:none;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:.72rem;gap:2px}
        @media(min-width:992px){body{padding-bottom:0}.mobile-nav{display:none!important}.desktop-layout{display:flex}.app-main{flex:1;min-width:0}}
        @media(max-width:991.98px){.desktop-side{display:none}.top-user{max-width:45vw;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.btn{min-height:44px}}
    </style>
</head>
<body>
<nav class="navbar navbar-dark topbar sticky-top">
    <div class="container-fluid app-shell">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">Chist</a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span id="netState" class="badge text-bg-success">Онлайн</span>
            @php($currentUser = auth()->user())
            @if($currentUser)
                <span class="text-white small top-user">{{ $currentUser->name }}</span>
            @endif
        </div>
    </div>
</nav>

@if($currentUser)
<div class="desktop-layout app-shell mx-auto">
    <aside class="desktop-side p-3">
        <div class="nav flex-column nav-pills gap-1">
            <a class="nav-link" href="{{ route('dashboard') }}">⌂ Главная</a>
            @if($currentUser->canDo('schedule.view'))
                <a class="nav-link" href="{{ route('journal.today') }}">▣ Журнал</a>
            @endif
            <a class="nav-link" href="{{ route('public.schedule') }}">📅 Расписание</a>
        </div>
    </aside>
@endif

<main class="container-fluid app-main py-3" style="max-width:950px">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @yield('content')
</main>

@if($currentUser)
</div>
<div class="mobile-nav d-lg-none">
    <div class="d-flex justify-content-around">
        <a class="navitem" href="{{ route('dashboard') }}"><span>⌂</span><span>Главная</span></a>
        <a class="navitem" href="{{ route('public.schedule') }}"><span>📅</span><span>Расписание</span></a>
        <form method="post" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button class="navitem border-0 bg-transparent" type="submit"><span>↪</span><span>Выйти</span></button>
        </form>
    </div>
</div>
@endif

<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));
}
function net() {
    const e = document.getElementById('netState');
    if (!e) return;
    e.textContent = navigator.onLine ? 'Онлайн' : 'Офлайн';
    e.className = 'badge ' + (navigator.onLine ? 'text-bg-success' : 'text-bg-danger');
}
window.addEventListener('online', net);
window.addEventListener('offline', net);
net();
</script>
<script src="/js/chist-charts.js"></script>
@stack('scripts')
</body>
</html>
