@php
    $first=$items->first();
    $split=$items->count()>1 && $items->contains(fn($x)=>$x->subgroup!==null);
@endphp
<tr>
    <td class="schedule-table-number">
        <div class="fw-bold fs-5">{{$pairNo}}</div>
        @if($first->starts_at)<div class="small text-secondary text-nowrap">{{substr($first->starts_at,0,5)}}–{{substr($first->ends_at,0,5)}}</div>@endif
    </td>
    <td class="p-0">
        <div class="{{$split?'schedule-table-subgroups':''}}">
        @foreach($items->sortBy(fn($x)=>$x->subgroup??-1) as $l)
            <div class="schedule-table-lesson p-3">
                <div class="d-flex justify-content-between gap-2 align-items-start">
                    <div>
                        @if($split)<span class="badge text-bg-light border mb-2">Подгруппа {{((int)$l->subgroup)+1}}</span>@endif
                        <div class="fw-bold">{{$l->subject_name}}</div>
                    </div>
                    @if($l->status==='replaced')<span class="badge text-bg-warning">Замена</span>@endif
                </div>
                <div class="small mt-1">
                    @if(!$split && $l->subgroup!==null)<span class="badge text-bg-light border me-2">Подгруппа {{((int)$l->subgroup)+1}}</span>@endif
                    @if($l->room_name)<span>каб. {{$l->room_name}}</span>@endif
                    @if($l->last_name)<span class="text-secondary"> · {{$l->last_name}} {{$l->first_name}} {{$l->middle_name}}</span>@endif
                </div>
            </div>
        @endforeach
        </div>
    </td>
</tr>
