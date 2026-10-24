@extends('layouts.app')
@section('title','Социальный паспорт')
@section('content')@include('upbringing._ui')<div class="up-shell">
<div class="ch-page-head"><div><a href="{{route('upbringing.index')}}">← Воспитательный отдел</a><h1 class="h4 mb-0">Социальный паспорт групп</h1><p>Автоматически рассчитывается по действующим социальным статусам студентов.</p></div><a class="btn btn-primary" href="{{route('upbringing.social-statuses')}}">Социальные статусы</a></div>
<div class="alert alert-info">Показатели не вводятся вручную. Измените социальный статус конкретного студента — сводка группы пересчитается автоматически.</div>
<div class="row g-3">
@foreach($groups as $g)
<div class="col-12 col-xl-6"><div class="card h-100"><div class="card-header d-flex justify-content-between"><b>{{$g->name}}</b><span class="badge text-bg-secondary">{{$g->student_count}} студентов</span></div><div class="card-body">
<div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span>Получают социальную поддержку</span><b>{{$g->social_support_count}}</b></div>
@if($g->categories->isEmpty())<div class="text-secondary">Действующих социальных категорий нет.</div>@else
@foreach($g->categories as $x)<div class="d-flex justify-content-between py-2 border-bottom"><span>{{$x->name}}</span><b>{{$x->total}}</b></div>@endforeach
@endif
</div></div></div>
@endforeach
</div>
</div>@endsection