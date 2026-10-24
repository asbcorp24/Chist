@extends('layouts.app')
@section('title','Анализ журнала')
@section('content')
<div class="ch-page-head"><div><a href="{{ route('journal.control') }}">← Контроль журналов</a><h1 class="h4 mb-0">{{ $g->name }} · {{ $sub->name }}</h1><p>Заполнение журнала и сводка студентов</p></div></div>
<div class="card mb-4"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Дата</th><th>Пара</th><th>Подгруппа</th><th>Тема</th><th>Посещ.</th><th>Оценок</th><th>Проверка</th></tr></thead><tbody>
@foreach($lessons as $x)<tr><td>{{ date('d.m.Y',strtotime($x->lesson_date)) }}</td><td>{{ $x->lesson_number }}</td><td>{{ $x->subgroup===null?'Вся группа':'№'.($x->subgroup+1) }}</td><td>{{ $x->topic ?: '—' }}</td><td>{{ $x->attendance }}</td><td>{{ $x->grades }}</td><td>{{ count($x->missing)?implode(', ',$x->missing):'✓' }}</td></tr>@endforeach
</tbody></table></div></div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Студент</th><th>Оценок</th><th>Средний</th><th>Н</th><th>Б</th><th>У</th><th>Опоздания</th></tr></thead><tbody>
@foreach($students as $s)<tr><td>{{ $s->last_name }} {{ $s->first_name }}</td><td>{{ $s->grade_count }}</td><td>{{ $s->average ?? '—' }}</td><td>{{ $s->absent }}</td><td>{{ $s->sick }}</td><td>{{ $s->excused }}</td><td>{{ $s->late }}</td></tr>@endforeach
</tbody></table></div></div>
@endsection