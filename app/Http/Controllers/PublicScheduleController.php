<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicScheduleController extends Controller
{
    public function mine(Request $r)
    {
        $u=$r->user();
        if($u->teacher_id){
            $r->merge(['teacher'=>(int)$u->teacher_id,'group'=>null]);
            return $this->index($r);
        }
        if($u->student_id){
            $groupId=DB::table('students')->where('id',$u->student_id)->value('current_group_id');
            abort_unless($groupId,404,'У студента не назначена учебная группа.');
            $r->merge(['group'=>(int)$groupId,'teacher'=>null]);
            return $this->index($r);
        }
        return redirect()->route('public.schedule');
    }

    public function index(Request $r)
    {
        $mode=$r->input('mode','day');
        if(!in_array($mode,['day','week'],true)) $mode='day';
        $date=Carbon::parse($r->input('date',now()->toDateString()));
        $group=$r->input('group');
        $teacher=$r->input('teacher');

        $from=$mode==='week' ? $date->copy()->startOfWeek(Carbon::MONDAY) : $date->copy();
        $to=$mode==='week' ? $from->copy()->addDays(6) : $date->copy();

        $q=DB::table('lesson_occurrences as l')
            ->join('study_groups as g','g.id','=','l.study_group_id')
            ->join('subjects as s','s.id','=','l.subject_id')
            ->leftJoin('rooms as rm','rm.id','=','l.room_id')
            ->leftJoin('teachers as t','t.id','=','l.teacher_id')
            ->leftJoin('lesson_times as lt',fn($j)=>$j->on('lt.academic_period_id','=','l.academic_period_id')->on('lt.lesson_number','=','l.lesson_number'))
            ->leftJoin('academic_periods as ap','ap.id','=','l.academic_period_id')
            ->whereBetween('l.lesson_date',[$from->toDateString(),$to->toDateString()])
            ->where('g.is_visible',1)
            ->where(fn($w)=>$w->whereNull('t.id')->orWhere('t.is_visible',1))
            ->where('l.status','!=','cancelled')
            ->select('l.id','l.lesson_date','l.lesson_number','l.subgroup','l.status','g.id as group_id','g.name as group_name',
                's.name as subject_name','rm.name as room_name','t.last_name','t.first_name','t.middle_name',
                'lt.starts_at','lt.ends_at','ap.lesson_unit_mode');

        if($group) $q->where('g.id',$group);
        if($teacher) $q->where('t.id',$teacher);

        $lessons=$q->orderBy('g.name')->orderBy('l.lesson_date')->orderBy('l.lesson_number')->get();
        $grouped=$lessons->groupBy('group_id')->map(function($items){
            $first=$items->first();
            return (object)[
                'id'=>$first->group_id,
                'name'=>$first->group_name,
                'days'=>$items->groupBy(fn($x)=>(string)$x->lesson_date)
            ];
        })->values();

        $days=[];
        for($d=$from->copy();$d->lte($to);$d->addDay()){
            $days[$d->toDateString()]=[
                'short'=>['Пн','Вт','Ср','Чт','Пт','Сб','Вс'][$d->isoWeekday()-1],
                'name'=>['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'][$d->isoWeekday()-1],
                'date'=>$d->format('d.m')
            ];
        }

        return view('public.schedule',[
            'date'=>$date->toDateString(),'mode'=>$mode,'from'=>$from,'to'=>$to,'days'=>$days,'scheduleGroups'=>$grouped,
            'groups'=>DB::table('study_groups')->where('active',1)->where('is_visible',1)->orderBy('name')->get(),
            'teachers'=>DB::table('teachers')->where('active',1)->where('is_visible',1)->orderBy('last_name')->get()
        ]);
    }
}
