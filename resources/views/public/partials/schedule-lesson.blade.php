<div class="schedule-lesson border rounded-3 p-2 p-lg-3 mb-2">
    <div class="d-flex gap-2 gap-lg-3">
        <div class="schedule-pair-no">{{$l->lesson_number}}</div>
        <div class="min-w-0 flex-grow-1">
            <div class="d-flex gap-2 justify-content-between align-items-start">
                <div class="fw-bold text-break">{{$l->subject_name}}</div>
                @if($l->status==='replaced')<span class="badge text-bg-warning flex-shrink-0">Замена</span>@endif
            </div>
            <div class="small mt-1">
                @if($l->lesson_unit_mode==='pair'){{$l->lesson_number}} пара@else{{$l->lesson_number}} урок@endif
                @if($l->subgroup!==null) · подгр. {{((int)$l->subgroup)+1}}@endif
                @if($l->room_name) · каб. {{$l->room_name}}@endif
            </div>
            <div class="small text-secondary mt-1">
                @if($l->starts_at){{substr($l->starts_at,0,5)}}–{{substr($l->ends_at,0,5)}}@endif
                @if($l->last_name) · {{$l->last_name}} {{$l->first_name}} {{$l->middle_name}}@endif
            </div>
        </div>
    </div>
</div>
