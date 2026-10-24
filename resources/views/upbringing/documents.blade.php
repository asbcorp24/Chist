@extends('layouts.app')
@section('title','Документы воспитательного отдела')
@section('content')
@include('upbringing._ui')<div class="up-shell">
<a href="{{route('upbringing.index')}}">← Воспитательный отдел</a>
<h1 class="h4">📑 Приказы и документы</h1>
<div class="alert alert-info">Новые документы отдела создавайте через единое ЭДО. Ниже остаётся связь с ранее созданными официальными документами.</div>
<form method="post" action="{{route('edo.integration.create',['upbringing','event',1])}}" class="up-create row g-2 mb-3">@csrf<div class="col-md-2">
<select name="document_type" class="form-select">
<option value="order">приказ</option>
<option value="memo">служебная записка</option>
<option value="protocol">протокол</option>
</select>
</div>
<div class="col-md-5">
<input name="title" class="form-control" required placeholder="Название документа">
</div>
<div class="col-md-3">
<input name="summary" class="form-control" placeholder="Краткое содержание">
</div>
<div class="col">
<button class="btn btn-dark">📑 Создать в ЭДО</button>
</div>
</form>
<p class="text-secondary">Связь официального документа Chist с мероприятием, сопровождением, Советом профилактики, собранием или поручением.</p>
<form method="post" action="{{route('upbringing.document.link')}}" class="up-create row g-2 mb-3">@csrf<div class="col-md-4">
<select name="official_document_id" class="form-select" required>
@foreach($docs as $d)<option value="{{$d->id}}">{{$d->number}} · {{$d->type}} · {{$d->status}}</option>
@endforeach
</select>
</div>
<div class="col-md-2">
<select name="entity_type" class="form-select">
<option value="event">мероприятие</option>
<option value="case">сопровождение</option>
<option value="council">совет</option>
<option value="meeting">собрание</option>
<option value="task">поручение</option>
</select>
</div>
<div class="col-md-2">
<input type="number" name="entity_id" min="1" class="form-control" required placeholder="ID записи">
</div>
<div class="col-md-2">
<select name="relation" class="form-select">
<option value="basis">основание</option>
<option value="order">приказ</option>
<option value="protocol">протокол</option>
<option value="result">итог</option>
</select>
</div>
<div class="col-md-2">
<button class="btn btn-primary w-100">Связать</button>
</div>
</form>
@foreach($rows as $x)<div class="border-bottom py-2">
<a href="{{route('documents.show',$x->official_document_id)}}">
<b>{{$x->number}}</b>
</a> · {{$x->entity_type}} #{{$x->entity_id}} · {{$x->relation}} · {{$x->status}}</div>
@endforeach
 </div>
@endsection
