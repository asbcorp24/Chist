@extends('layouts.app')
@section('title','Управление воспитательной работой')
@section('content')
@include('upbringing._ui')<div class="up-shell">
<a href="{{route('upbringing.index')}}">← Воспитательный отдел</a>
<h1 class="h4">🎛 Заместитель директора по воспитательной работе</h1>
<div class="row g-2">
@foreach(['planned'=>'Запланировано','completed'=>'Проведено','open_cases'=>'Сопровождения','overdue_cases'=>'Просрочен контроль','open_tasks'=>'Поручения','overdue_tasks'=>'Просрочено поручений'] as $k=>$n)<div class="col-md-2">
<div class="up-panel {{$stats[$k]&&str_contains($k,'overdue')?'border-danger':''}}">
<small>{{$n}}</small>
<b class="h3">{{$stats[$k]}}</b>
</div>
</div>
@endforeach
</div>
<h2 class="h6 mt-4">Контроль поручений</h2>
@foreach($tasks as $x)<div class="border-bottom py-2">
<b>{{$x->title}}</b> · {{$x->assigned_name}} · до {{$x->due_on}} · {{$x->status}}</div>
@endforeach
<h2 class="h6 mt-4">Направления воспитательной работы</h2>
@foreach($dirs as $x)<div class="d-flex justify-content-between border-bottom py-2">
<span>{{$x->direction}}</span>
<span>{{$x->completed}} / {{$x->total}}</span>
</div>
@endforeach
 </div>
@endsection
