@extends('layouts.public')
@section('title','Расписание')
@section('content')
@php
    $prev=$mode==='week'?$from->copy()->subWeek()->toDateString():$from->copy()->subDay()->toDateString();
    $next=$mode==='week'?$from->copy()->addWeek()->toDateString():$from->copy()->addDay()->toDateString();
@endphp
<div class="schedule-page">
<div class="card border-0 shadow-sm mb-3"><div class="card-body p-3 p-lg-4">
    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center mb-3">
        <div><h1 class="h3 mb-1">Расписание</h1><div class="text-secondary">По группам, дням и парам</div></div>
        <div class="btn-group" role="group">
            <a class="btn {{$mode==='day'?'btn-primary':'btn-outline-primary'}}" href="{{request()->fullUrlWithQuery(['mode'=>'day','date'=>$date])}}">День</a>
            <a class="btn {{$mode==='week'?'btn-primary':'btn-outline-primary'}}" href="{{request()->fullUrlWithQuery(['mode'=>'week','date'=>$date])}}">Расписание на неделю</a>
        </div>
    </div>
    <form class="row g-2 align-items-end">
        <input type="hidden" name="mode" value="{{$mode}}">
        <div class="col-12 col-md-3"><label class="form-label small">Дата</label><input class="form-control" type="date" name="date" value="{{$date}}"></div>
        <div class="col-6 col-md-4"><label class="form-label small">Группа</label><select class="form-select" name="group"><option value="">Все группы</option>@foreach($groups as $g)<option value="{{$g->id}}" @selected(request('group')==$g->id)>{{$g->name}}</option>@endforeach</select></div>
        <div class="col-6 col-md-4"><label class="form-label small">Преподаватель</label><select class="form-select" name="teacher"><option value="">Все преподаватели</option>@foreach($teachers as $t)<option value="{{$t->id}}" @selected(request('teacher')==$t->id)>{{$t->last_name}} {{$t->first_name}}</option>@endforeach</select></div>
        <div class="col-12 col-md-1 d-grid"><button class="btn btn-primary">Показать</button></div>
    </form>
</div></div>

<div class="d-flex align-items-center justify-content-between gap-2 mb-3">
    <a class="btn btn-outline-secondary" href="{{request()->fullUrlWithQuery(['date'=>$prev])}}">‹</a>
    <div class="text-center fw-semibold">
        @if($mode==='week') Неделя {{$from->format('d.m')}} — {{$to->format('d.m.Y')}} @else {{($days[$date]['name']??'')}} · {{date('d.m.Y',strtotime($date))}} @endif
    </div>
    <a class="btn btn-outline-secondary" href="{{request()->fullUrlWithQuery(['date'=>$next])}}">›</a>
</div>

@if($mode==='week')
<div class="schedule-days-strip d-flex gap-2 overflow-auto pb-2 mb-3">
@foreach($days as $dayDate=>$day)
<a href="{{request()->fullUrlWithQuery(['mode'=>'day','date'=>$dayDate])}}" class="btn btn-light border flex-shrink-0">
    <strong>{{$day['short']}}</strong><span class="ms-1">{{$day['date']}}</span>
</a>
@endforeach
</div>
@endif

@forelse($scheduleGroups as $sg)
<section class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-header bg-body py-3 px-3 px-lg-4 d-flex align-items-center justify-content-between">
        <div><div class="text-secondary small">Группа</div><h2 class="h4 mb-0">{{$sg->name}}</h2></div>
        <span class="badge rounded-pill text-bg-light border">{{$sg->days->flatten()->count()}} занятий</span>
    </div>
    <div class="card-body p-3 p-lg-4">
        @if($mode==='week')
        <div class="row g-3">
        @foreach($days as $dayDate=>$day)
            @php $items=$sg->days->get($dayDate,collect()); @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="schedule-day h-100 border rounded-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3"><strong>{{$day['name']}}</strong><span class="text-secondary small">{{$day['date']}}</span></div>
                    @forelse($items as $l)
                        @include('public.partials.schedule-lesson',['l'=>$l])
                    @empty
                        <div class="text-secondary small py-2">Занятий нет</div>
                    @endforelse
                </div>
            </div>
        @endforeach
        </div>
        @else
            @php $items=$sg->days->get($date,collect()); @endphp
            <div class="row g-2">@foreach($items as $l)<div class="col-12 col-lg-6">@include('public.partials.schedule-lesson',['l'=>$l])</div>@endforeach</div>
        @endif
    </div>
</section>
@empty
<div class="alert alert-light border text-center py-5"><div class="h5">Занятий нет</div><div class="text-secondary">Для выбранного периода расписание не найдено.</div></div>
@endforelse
<div class="text-center text-secondary small mt-4">Оценки, посещаемость и персональные данные доступны только после входа.</div>
</div>
<style>
.schedule-page{max-width:1500px;margin:0 auto}.schedule-day{background:var(--bs-tertiary-bg)}.schedule-days-strip{-webkit-overflow-scrolling:touch}
.schedule-lesson{background:var(--bs-body-bg);transition:.15s ease}.schedule-lesson:hover{transform:translateY(-1px);box-shadow:0 .25rem .75rem rgba(0,0,0,.06)}
.schedule-pair-no{width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:14px;background:var(--bs-primary-bg-subtle);color:var(--bs-primary-text-emphasis);font-weight:800;flex:0 0 48px}
@media(max-width:575.98px){.card-body{padding-left:.75rem!important;padding-right:.75rem!important}.schedule-pair-no{width:42px;height:42px;flex-basis:42px;border-radius:12px}}
</style>
@endsection
