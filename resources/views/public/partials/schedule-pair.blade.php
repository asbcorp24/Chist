@php
    $first=$items->first();
    $split=$items->count()>1 && $items->contains(fn($x)=>$x->subgroup!==null);
@endphp
<div class="schedule-pair border rounded-3 mb-2 overflow-hidden">
    <div class="schedule-pair-head d-flex align-items-center gap-2 px-2 px-lg-3 py-2">
        <div class="schedule-pair-no">{{$pairNo}}</div>
        <div class="min-w-0">
            <div class="fw-bold">{{$first->lesson_unit_mode==='pair'?$pairNo.' пара':$pairNo.' урок'}}</div>
            @if($first->starts_at)<div class="small text-secondary">{{substr($first->starts_at,0,5)}}–{{substr($first->ends_at,0,5)}}</div>@endif
        </div>
        @if($split)<span class="badge rounded-pill text-bg-primary ms-auto">{{$items->count()}} подгруппы</span>@endif
    </div>
    <div class="{{$split?'schedule-subgroups':''}}">
    @foreach($items->sortBy(fn($x)=>$x->subgroup??-1) as $l)
        <div class="schedule-subgroup p-2 p-lg-3">
            @if($split)
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge text-bg-light border">Подгруппа {{((int)$l->subgroup)+1}}</span>
                @if($l->status==='replaced')<span class="badge text-bg-warning">Замена</span>@endif
            </div>
            @elseif($l->status==='replaced')<div class="mb-2"><span class="badge text-bg-warning">Замена</span></div>@endif
            <div class="fw-bold text-break">{{$l->subject_name}}</div>
            <div class="small mt-1">
                @if(!$split && $l->subgroup!==null)<span class="badge text-bg-light border me-1">Подгруппа {{((int)$l->subgroup)+1}}</span>@endif
                @if($l->room_name)каб. {{$l->room_name}}@endif
            </div>
            @if($l->last_name)<div class="small text-secondary mt-1">{{$l->last_name}} {{$l->first_name}} {{$l->middle_name}}</div>@endif
        </div>
    @endforeach
    </div>
</div>
