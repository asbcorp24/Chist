<?php
namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MySchedule extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Моё расписание';
    protected static ?string $navigationGroup = 'Рабочее пространство';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.my-schedule';

    public string $date = '';
    public string $mode = 'day';
    public array $days = [];
    public array $scheduleGroups = [];

    protected $queryString = ['date', 'mode'];

    public function mount(): void { $this->date = $this->date ?: now()->toDateString(); $this->loadSchedule(); }
    public function updatedDate(): void { $this->loadSchedule(); }
    public function updatedMode(): void { if(!in_array($this->mode,['day','week'],true)) $this->mode='day'; $this->loadSchedule(); }
    public function previous(): void { $d=Carbon::parse($this->date); $this->date=($this->mode==='week'?$d->subWeek():$d->subDay())->toDateString(); $this->loadSchedule(); }
    public function next(): void { $d=Carbon::parse($this->date); $this->date=($this->mode==='week'?$d->addWeek():$d->addDay())->toDateString(); $this->loadSchedule(); }
    public function selectDay(string $date): void { $this->mode='day'; $this->date=$date; $this->loadSchedule(); }

    protected function loadSchedule(): void
    {
        $u=auth()->user(); if(!$u) return;
        $date=Carbon::parse($this->date); $from=$this->mode==='week'?$date->copy()->startOfWeek(Carbon::MONDAY):$date->copy(); $to=$this->mode==='week'?$from->copy()->addDays(6):$date->copy();
        $q=DB::table('lesson_occurrences as l')->join('study_groups as g','g.id','=','l.study_group_id')->join('subjects as s','s.id','=','l.subject_id')
            ->leftJoin('rooms as rm','rm.id','=','l.room_id')->leftJoin('teachers as t','t.id','=','l.teacher_id')
            ->leftJoin('lesson_times as lt',fn($j)=>$j->on('lt.academic_period_id','=','l.academic_period_id')->on('lt.lesson_number','=','l.lesson_number'))
            ->leftJoin('academic_periods as ap','ap.id','=','l.academic_period_id')
            ->whereBetween('l.lesson_date',[$from->toDateString(),$to->toDateString()])->where('l.status','!=','cancelled')
            ->select('l.id','l.lesson_date','l.lesson_number','l.subgroup','l.status','g.id as group_id','g.name as group_name','s.name as subject_name','rm.name as room_name','t.last_name','t.first_name','t.middle_name','lt.starts_at','lt.ends_at','ap.lesson_unit_mode');
        if($u->teacher_id) $q->where('l.teacher_id',$u->teacher_id);
        elseif($u->student_id){$gid=DB::table('students')->where('id',$u->student_id)->value('current_group_id'); if(!$gid){$this->scheduleGroups=[];return;} $q->where('l.study_group_id',$gid);}
        else { $this->scheduleGroups=[]; return; }
        $items=$q->orderBy('g.name')->orderBy('l.lesson_date')->orderBy('l.lesson_number')->get();
        $this->scheduleGroups=$items->groupBy('group_id')->map(function($x){$f=$x->first();return ['id'=>$f->group_id,'name'=>$f->group_name,'days'=>$x->groupBy(fn($l)=>(string)$l->lesson_date)->map(fn($d)=>$d->map(fn($l)=>(array)$l)->all())->all()];})->values()->all();
        $this->days=[]; for($d=$from->copy();$d->lte($to);$d->addDay()){$this->days[$d->toDateString()]=['short'=>['Пн','Вт','Ср','Чт','Пт','Сб','Вс'][$d->isoWeekday()-1],'name'=>['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'][$d->isoWeekday()-1],'date'=>$d->format('d.m')];}
    }

    public static function canAccess(): bool { $u=auth()->user(); return $u && ($u->teacher_id || $u->student_id); }
}
