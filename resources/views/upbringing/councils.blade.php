@extends('layouts.app')
@section('title','Совет профилактики')
@section('content')
@include('upbringing._ui')
<div class="up-shell">
  <div class="up-head">
    <div>
      <a class="up-breadcrumb" href="{{ route('upbringing.index') }}">← Воспитательный отдел</a>
      <h1>Совет профилактики</h1>
      <div class="up-sub">Заседания, решения и контроль исполнения</div>
    </div>
  </div>

  <form method="post" action="{{ route('upbringing.council.store') }}" class="up-create row g-2">
    @csrf
    <div class="col-md-2"><input class="form-control" name="protocol_number" required placeholder="Протокол №"></div>
    <div class="col-md-2"><input type="date" class="form-control" name="meeting_on" required></div>
    <div class="col-md-6"><input class="form-control" name="agenda" placeholder="Повестка заседания"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Создать заседание</button></div>
  </form>

  @forelse($rows as $x)
    <div class="up-panel">
      <div class="d-flex flex-wrap justify-content-between gap-2 align-items-start">
        <div><b>Протокол №{{ $x->protocol_number }}</b><div class="up-sub">{{ $x->meeting_on }} · {{ $x->agenda }}</div></div>
        <div class="d-flex gap-2">
          <a target="_blank" class="btn btn-sm btn-outline-secondary" href="{{ route('upbringing.council.print',$x->id) }}">Печать</a>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#edit-council-{{ $x->id }}">Изменить</button>
          <form method="post" action="{{ route('upbringing.council.archive',$x->id) }}">@csrf<button class="btn btn-sm btn-outline-danger">В архив</button></form>
        </div>
      </div>

      <div class="collapse mt-3" id="edit-council-{{ $x->id }}">
        <form method="post" action="{{ route('upbringing.council.update',$x->id) }}" class="up-create row g-2 mb-0">
          @csrf @method('PUT')
          <div class="col-md-2"><input class="form-control" name="protocol_number" value="{{ $x->protocol_number }}" required></div>
          <div class="col-md-2"><input type="date" class="form-control" name="meeting_on" value="{{ $x->meeting_on }}" required></div>
          <div class="col-md-6"><input class="form-control" name="agenda" value="{{ $x->agenda }}"></div>
          <div class="col-md-2"><button class="btn btn-primary w-100">Сохранить</button></div>
        </form>
      </div>

      <form method="post" action="{{ route('upbringing.council.case',$x->id) }}" class="row g-2 mt-3">
        @csrf
        <div class="col-md-4"><select class="form-select" name="student_support_case_id" required><option value="">Добавить случай сопровождения</option>@foreach($cases as $case)<option value="{{ $case->id }}">{{ $case->last_name }} {{ $case->first_name }} — {{ $case->reason }}</option>@endforeach</select></div>
        <div class="col-md-5"><input class="form-control" name="decision" required placeholder="Решение совета"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="control_on"></div>
        <div class="col-md-1"><button class="btn btn-outline-primary w-100">+</button></div>
      </form>
    </div>
  @empty
    <div class="up-panel up-empty">Заседаний пока нет</div>
  @endforelse
</div>
@endsection
