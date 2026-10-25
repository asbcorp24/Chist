<x-filament::page>
<div class="space-y-5">
 <div class="rounded-xl bg-white p-5 shadow">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
   <div><h2 class="text-xl font-bold">Моё расписание</h2><p class="text-sm text-gray-500">Персональное расписание занятий</p></div>
   <div class="flex gap-2"><x-filament::button :color="$mode==='day'?'primary':'secondary'" wire:click="$set('mode','day')">День</x-filament::button><x-filament::button :color="$mode==='week'?'primary':'secondary'" wire:click="$set('mode','week')">Неделя</x-filament::button></div>
  </div>
  <div class="mt-5 max-w-xs"><label class="mb-1 block text-sm font-medium">Дата</label><input type="date" wire:model="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></div>
 </div>
 <div class="flex items-center justify-between gap-3">
  <x-filament::button color="secondary" wire:click="previous">←</x-filament::button>
  <div class="text-center font-semibold">@if($mode==='week') Неделя @else {{ $days[$date]['name'] ?? '' }} · @endif {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</div>
  <x-filament::button color="secondary" wire:click="next">→</x-filament::button>
 </div>
 @if($mode==='week')<div class="flex gap-2 overflow-x-auto pb-1">@foreach($days as $dayDate=>$day)<button wire:click="selectDay('{{$dayDate}}')" class="shrink-0 rounded-lg border bg-white px-4 py-2 text-sm shadow-sm"><b>{{$day['short']}}</b> {{$day['date']}}</button>@endforeach</div>@endif
 @forelse($scheduleGroups as $sg)
 <section class="overflow-hidden rounded-xl bg-white shadow">
  <div class="flex items-center justify-between border-b px-5 py-4"><div><div class="text-xs text-gray-500">Группа</div><h3 class="text-lg font-bold">{{$sg['name']}}</h3></div></div>
  <div class="p-4">
   @foreach($days as $dayDate=>$day)
    @php $items=collect($sg['days'][$dayDate]??[]); @endphp
    @if($mode==='week')<div class="mb-2 mt-3 flex justify-between rounded-lg bg-gray-50 px-3 py-2"><b>{{$day['name']}}</b><span class="text-gray-500">{{$day['date']}}</span></div>@endif
    @forelse($items->groupBy('lesson_number') as $pairNo=>$pairItems)
     @php $first=(object)$pairItems->first(); @endphp
     <div class="mb-3 rounded-xl border p-4">
      <div class="flex flex-col gap-3 md:flex-row">
       <div class="md:w-32"><div class="text-lg font-bold">{{$pairNo}} {{$first->lesson_unit_mode==='pair'?'пара':'урок'}}</div>@if($first->starts_at)<div class="text-sm text-gray-500">{{substr($first->starts_at,0,5)}}–{{substr($first->ends_at,0,5)}}</div>@endif</div>
       <div class="grid flex-1 gap-3 {{count($pairItems)>1?'md:grid-cols-2':''}}">@foreach($pairItems as $raw) @php $l=(object)$raw; @endphp
        <div class="rounded-lg bg-gray-50 p-3">@if($l->subgroup!==null)<span class="mb-2 inline-block rounded-full bg-gray-200 px-2 py-1 text-xs">Подгруппа {{((int)$l->subgroup)+1}}</span>@endif @if($l->status==='replaced')<span class="mb-2 inline-block rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-700">Замена</span>@endif
         <div class="font-semibold">{{$l->subject_name}}</div><div class="mt-1 text-sm">@if($l->room_name)каб. {{$l->room_name}}@endif</div>@if($l->last_name)<div class="mt-1 text-sm text-gray-500">{{$l->last_name}} {{$l->first_name}} {{$l->middle_name}}</div>@endif
        </div>@endforeach</div>
      </div>
     </div>
    @empty @if($mode==='week')<div class="px-3 py-3 text-sm text-gray-400">Занятий нет</div>@endif @endforelse
   @endforeach
  </div>
 </section>
 @empty <div class="rounded-xl bg-white p-10 text-center shadow"><div class="text-lg font-semibold">Занятий нет</div><div class="mt-1 text-sm text-gray-500">Для выбранной даты расписание не найдено.</div></div>
 @endforelse
</div>
</x-filament::page>