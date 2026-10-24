@extends('layouts.app')
@section('title','Сопровождение')
@section('content')
@include('upbringing._ui')<div class="up-shell">@include('upbringing._ui')<div class="up-shell">
<a href="{{route('upbringing.cases')}}">← Индивидуальная работа</a>
<h1 class="h4">{{$x->last_name}} {{$x->first_name}} {{$x->middle_name}}</h1>
<details class="up-panel" open>
<summary class="fw-bold">Карточка сопровождения</summary>
<form method="post" action="{{route('upbringing.case.update',$x->id)}}" class="row g-2 mt-3">@csrf @method('PUT')<div class="col-md-3">
<select name="category" class="form-select">
<option value="{{$x->category}}">{{$x->category}}</option>
<option value="individual">индивидуальная работа</option>
<option value="attendance">посещаемость</option>
<option value="behavior">поведение</option>
<option value="prevention">профилактика</option>
<option value="family">семья</option>
<option value="other">иное</option>
</select>
</div>
<div class="col-md-2">
<select name="priority" class="form-select">
<option value="{{$x->priority}}">{{$x->priority}}</option>
<option value="normal">обычный</option>
<option value="high">высокий</option>
<option value="critical">критичный</option>
</select>
</div>
<div class="col-md-2">
<input type="date" name="next_contact_on" value="{{$x->next_contact_on}}" class="form-control">
</div>
<div class="col-12">
<textarea name="reason" class="form-control" required>{{$x->reason}}</textarea>
</div>
<div class="col-12">
<textarea name="plan" class="form-control">{{$x->plan}}</textarea>
</div>
<div class="col-md-2">
<button class="btn btn-primary">Сохранить</button>
</div>
</form>
</details>
@if($x->status!=='closed')<form method="post" action="{{route('upbringing.action',$x->id)}}" class="up-create row g-2 mb-3">@csrf<div class="col-md-3">
<select class="form-select" name="action_type">
<option value="conversation">беседа</option>
<option value="parent_contact">контакт с семьёй</option>
<option value="referral">направление к специалисту</option>
<option value="meeting">встреча</option>
<option value="monitoring">контроль</option>
<option value="document">документ</option>
<option value="other">иное</option>
</select>
</div>
<div class="col-md-6">
<input class="form-control" name="note" required placeholder="Что сделано / результат">
</div>
<div class="col-md-2">
<input type="date" class="form-control" name="next_contact_on">
</div>
<div class="col-md-1">
<button class="btn btn-primary w-100">+</button>
</div>
</form>
<form method="post" action="{{route('upbringing.case.close',$x->id)}}" class="mb-3">@csrf<button class="btn btn-outline-success">Закрыть сопровождение</button>
</form>
@endif
<h2 class="h6">История работы</h2>
@foreach($actions as $a)<div class="border-bottom py-2">{{$a->acted_at}} · <b>{{$a->action_type}}</b> · {{$a->note}}</div>
@endforeach
 </div>
</div>
@endsection
