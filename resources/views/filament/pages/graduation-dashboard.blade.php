<x-filament::page>
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
<div class="rounded-xl bg-white p-5 shadow">Выпускники: <b>{{$stats['total']??0}}</b></div>
<div class="rounded-xl bg-white p-5 shadow">Готовы: <b>{{$stats['ready']??0}}</b></div>
<div class="rounded-xl bg-white p-5 shadow">Заблокированы: <b>{{$stats['blocked']??0}}</b></div>
<div class="rounded-xl bg-white p-5 shadow">Подготовлены: <b>{{$stats['prepared']??0}}</b></div>
</div>
<div class="mt-4 rounded-xl bg-white p-4 shadow"><div class="mb-4 flex justify-between"><h2 class="text-lg font-bold">Реестр выпуска</h2><a href="/academic-office/graduation" class="rounded-lg bg-blue-600 px-4 py-2 text-white">Управление выпуском</a></div>
@foreach($rows as $x)<a href="/academic-office/graduation/{{$x['id']}}" class="mb-2 block rounded-lg border p-3"><b>{{$x['last_name']}} {{$x['first_name']}}</b> · {{$x['group_name']}}<div class="text-sm text-gray-500">Готовность: {{$x['readiness']??'не проверено'}} · Документы: {{$x['documents']}} · Выдано: {{$x['issued']}}</div></a>@endforeach
</div>
</x-filament::page>
