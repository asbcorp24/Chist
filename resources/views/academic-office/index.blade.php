@extends('layouts.app')
@section('title','Учебная часть')
@section('content')<div class="mb-3"><a class="btn btn-primary" href="{{route('admission.index')}}">Приёмная комиссия · Абитуриенты</a></div>
<div class="ao-head"><div><div class="text-secondary small mb-1">Учебный процесс · {{$p?->year_name ?? 'период не настроен'}}@if($p) · {{$p->name}}@endif</div><h1 class="h3 mb-1">Учебная часть</h1><p class="text-secondary mb-0">Контингент, успеваемость, контроль, аттестация, экзамены, ГИА и выпуск.</p></div><div class="ao-head-actions"><a href="{{route('academic.control')}}" class="btn btn-primary">Учебный контроль</a><a href="{{route('reports.groups')}}" class="btn btn-outline-secondary">Отчёты</a></div></div>

<div class="ao-kpi">
<div class="ao-kpi-card"><span>Студенты</span><b>{{$stats['studentCount']}}</b><small>активный контингент</small></div>
<div class="ao-kpi-card"><span>Группы</span><b>{{$stats['groupCount']}}</b><small>действующие</small></div>
<div class="ao-kpi-card {{$stats['debts']?'is-warn':''}}"><span>Задолженности</span><b>{{$stats['debts']}}</b><small>требуют контроля</small></div>
<div class="ao-kpi-card {{$stats['openSheets']?'is-info':''}}"><span>Открытые ведомости</span><b>{{$stats['openSheets']}}</b><small>аттестация</small></div>
</div>

<div class="ao-mobile-tabs">
<a href="{{route('academic.control')}}">Контроль</a><a href="{{route('attestation.index')}}">Экзамены</a><a href="{{route('gia.index')}}">ГИА</a><a href="{{route('graduation.index')}}">Выпуск</a><a href="{{route('study-plan.index')}}">Планы</a>
</div>

<h2 class="ao-section-title">Основные процессы</h2>
<div class="ao-modules">
<a class="ao-module" href="{{route('admin.students.index')}}"><i>👥</i><div><b>Контингент</b><span>Студенты, карточки, статусы и движение</span></div><em>→</em></a>
<a class="ao-module" href="{{route('academic.progress')}}"><i>📊</i><div><b>Успеваемость</b><span>Оценки, средний балл, задолженности</span></div><em>→</em></a>
<a class="ao-module" href="{{route('academic.control')}}"><i>✓</i><div><b>Учебный контроль</b><span>Допуски, задолженности, выполнение</span></div><em>→</em></a>
<a class="ao-module" href="{{route('attestation.index')}}"><i>📝</i><div><b>Аттестация и экзамены</b><span>Ведомости, зачёты, экзамены</span></div><em>→</em></a>
<a class="ao-module" href="{{route('attestation.index')}}"><i>↻</i><div><b>Пересдачи</b><span>Попытки, комиссии, закрытие долгов</span></div><em>→</em></a>
<a class="ao-module" href="{{route('gia.index')}}"><i>🎓</i><div><b>ГИА</b><span>Кампании, комиссии и результаты</span></div><em>→</em></a>
<a class="ao-module" href="{{route('graduation.index')}}"><i>🏅</i><div><b>Выпуск</b><span>Допуск, дипломы, итоговые документы</span></div><em>→</em></a>
<a class="ao-module" href="{{route('academic.movements')}}"><i>⇄</i><div><b>Переводы</b><span>Группы, курс, семестр и движение студентов</span></div><em>→</em></a>
<a class="ao-module" href="{{route('study-plan.index')}}"><i>📚</i><div><b>Учебные планы</b><span>Дисциплины, часы и формы контроля</span></div><em>→</em></a>
<a class="ao-module" href="{{route('academic.plans')}}"><i>👤</i><div><b>Индивидуальные планы</b><span>ИУП студентов, дисциплины, часы и контроль</span></div><em>→</em></a>
<a class="ao-module" href="{{route('loads.index')}}"><i>◷</i><div><b>Нагрузка</b><span>Преподаватели, группы и часы</span></div><em>→</em></a>
<a class="ao-module" href="{{route('academic.orders')}}"><i>📑</i><div><b>Учебные приказы</b><span>Зачисление, перевод, отпуск, отчисление и выпуск</span></div><em>→</em></a>
<a class="ao-module" href="{{route('regulations.index')}}"><i>§</i><div><b>Локальные регламенты</b><span>Положения, правила и нормативные документы</span></div><em>→</em></a>
<a class="ao-module" href="{{route('reports.groups')}}"><i>▤</i><div><b>Отчёты</b><span>Группы, студенты и печатные формы</span></div><em>→</em></a>
</div>

<div class="row g-3 mt-2">
<div class="col-xl-7"><div class="card ao-attention"><div class="card-header"><b>Требует внимания</b></div><div class="card-body p-0">
<a href="{{route('academic.control')}}"><span class="dot danger"></span><div><b>{{$stats['debts']}} академических задолженностей</b><small>Проверить допуски и назначить пересдачи</small></div><strong>→</strong></a>
<a href="{{route('attestation.index')}}"><span class="dot warning"></span><div><b>{{$stats['openSheets']}} открытых ведомостей</b><small>Проверить заполнение результатов аттестации</small></div><strong>→</strong></a>
<a href="{{route('gia.index')}}"><span class="dot info"></span><div><b>{{$stats['giaCount']}} кампаний ГИА</b><small>Комиссии, мероприятия и результаты</small></div><strong>→</strong></a>
</div></div></div>
<div class="col-xl-5"><div class="card"><div class="card-header d-flex justify-content-between"><b>Последние приказы</b><span class="text-secondary small">{{$stats['movementCount']}} движений</span></div><div class="card-body p-0">@forelse($orders as $o)<div class="ao-order"><b>№ {{$o->number}}</b><span>{{$o->title}}</span><small>{{date('d.m.Y',strtotime($o->order_date))}}</small></div>@empty<div class="p-4 text-secondary">Приказов пока нет.</div>@endforelse</div></div></div>
</div>
@endsection