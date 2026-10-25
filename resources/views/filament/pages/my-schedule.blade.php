<x-filament::page>
<div class="space-y-4">
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="flex flex-col gap-4 p-4 lg:flex-row lg:items-end lg:justify-between lg:p-5">
            <div class="flex flex-1 flex-col gap-4 sm:flex-row sm:items-end">
                <div class="w-full sm:max-w-[220px]">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Дата</label>
                    <input type="date" wire:model="date" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <div class="mb-1.5 text-sm font-medium text-gray-700">Показывать</div>
                    <div class="inline-flex rounded-lg bg-gray-100 p-1">
                        <button wire:click="$set('mode','day')" class="rounded-md px-4 py-2 text-sm font-semibold transition {{ $mode==='day' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">День</button>
                        <button wire:click="$set('mode','week')" class="rounded-md px-4 py-2 text-sm font-semibold transition {{ $mode==='week' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">Неделя</button>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between gap-2 lg:justify-end">
                <button wire:click="previous" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium shadow-sm hover:bg-gray-50">←</button>
                <div class="min-w-[190px] text-center">
                    <div class="text-xs uppercase tracking-wide text-gray-400">{{ $mode==='week' ? 'Учебная неделя' : 'Выбранный день' }}</div>
                    <div class="font-semibold text-gray-900">@if($mode==='week'){{ reset($days)['date'] ?? '' }} — {{ end($days)['date'] ?? '' }}@else{{ $days[$date]['name'] ?? '' }}, {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}@endif</div>
                </div>
                <button wire:click="next" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium shadow-sm hover:bg-gray-50">→</button>
            </div>
        </div>
        @if($mode==='week')
        <div class="grid grid-cols-7 border-t bg-gray-50/70">
            @foreach($days as $dayDate=>$day)
            <button wire:click="selectDay('{{$dayDate}}')" class="border-r px-2 py-3 text-center last:border-r-0 hover:bg-white">
                <div class="text-xs font-semibold uppercase text-gray-400">{{$day['short']}}</div>
                <div class="mt-0.5 text-sm font-semibold text-gray-800">{{$day['date']}}</div>
            </button>
            @endforeach
        </div>
        @endif
    </div>

    @forelse($scheduleGroups as $sg)
    <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="flex items-center gap-3 border-b px-4 py-3 lg:px-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-sm font-bold text-primary-600">{{$sg['name']}}</div>
            <div><div class="text-xs text-gray-400">Учебная группа</div><div class="font-semibold text-gray-900">{{$sg['name']}}</div></div>
        </div>
        <div class="divide-y">
        @foreach($days as $dayDate=>$day)
            @php $items=collect($sg['days'][$dayDate]??[]); @endphp
            @if($mode==='week')
            <div class="bg-gray-50 px-4 py-2.5 lg:px-5"><span class="font-semibold text-gray-800">{{$day['name']}}</span><span class="ml-2 text-sm text-gray-400">{{$day['date']}}</span></div>
            @endif
            @forelse($items->groupBy('lesson_number') as $pairNo=>$pairItems)
            @php $first=(object)$pairItems->first(); @endphp
            <div class="grid gap-3 px-4 py-4 lg:grid-cols-[110px_1fr] lg:px-5">
                <div class="flex items-center gap-3 lg:block">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 font-bold text-primary-700 lg:mb-1">{{$pairNo}}</div>
                    <div><div class="text-sm font-semibold text-gray-700">{{$first->lesson_unit_mode==='pair'?'пара':'урок'}}</div>@if($first->starts_at)<div class="text-xs text-gray-400">{{substr($first->starts_at,0,5)}}–{{substr($first->ends_at,0,5)}}</div>@endif</div>
                </div>
                <div class="grid gap-2 {{count($pairItems)>1?'md:grid-cols-2':''}}">
                @foreach($pairItems as $raw) @php $l=(object)$raw; @endphp
                    <div class="rounded-lg border border-gray-200 p-3">
                        <div class="flex flex-wrap items-center gap-2">
                            @if($l->subgroup!==null)<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Подгруппа {{((int)$l->subgroup)+1}}</span>@endif
                            @if($l->status==='replaced')<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Замена</span>@endif
                        </div>
                        <div class="mt-1.5 font-semibold text-gray-900">{{$l->subject_name}}</div>
                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                            @if($l->room_name)<span>Кабинет <b class="font-medium text-gray-700">{{$l->room_name}}</b></span>@endif
                            @if($l->last_name)<span>{{$l->last_name}} {{$l->first_name}} {{$l->middle_name}}</span>@endif
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            @empty
                @if($mode==='week')<div class="px-5 py-3 text-sm text-gray-400">Занятий нет</div>@endif
            @endforelse
        @endforeach
        </div>
    </section>
    @empty
    <div class="rounded-xl bg-white px-6 py-12 text-center shadow-sm ring-1 ring-gray-950/5">
        <div class="text-base font-semibold text-gray-800">На выбранную дату занятий нет</div>
        <div class="mt-1 text-sm text-gray-400">Переключите день или неделю.</div>
    </div>
    @endforelse
</div>
</x-filament::page>