<?php
namespace App\Services;
use Carbon\Carbon; use Illuminate\Support\Facades\DB;
class LessonOccurrenceGenerator {
 public function generate(int $periodId): int {$n=0;$holidays=DB::table('calendar_exceptions')->where('academic_period_id',$periodId)->where('type','holiday')->pluck('exception_date')->map(fn($x)=>(string)$x)->flip();foreach(DB::table('schedule_entries')->where('academic_period_id',$periodId)->where('active',1)->cursor() as $e){$d=Carbon::parse($e->starts_on);$end=Carbon::parse($e->ends_on);while($d->lte($end)){if($d->isoWeekday()==$e->weekday&&!isset($holidays[$d->toDateString()])){DB::table('lesson_occurrences')->updateOrInsert(['schedule_entry_id'=>$e->id,'lesson_date'=>$d->toDateString()],['academic_period_id'=>$e->academic_period_id,'study_group_id'=>$e->study_group_id,'subgroup'=>$e->subgroup,'teacher_id'=>$e->teacher_id,'subject_id'=>$e->subject_id,'room_id'=>$e->room_id,'lesson_number'=>$e->lesson_number,'status'=>'planned','updated_at'=>now(),'created_at'=>now()]);$n++;}$d->addDay();}}return $n;}
}
