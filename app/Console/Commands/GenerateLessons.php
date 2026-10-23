<?php
namespace App\Console\Commands;
use Illuminate\Console\Command; use Illuminate\Support\Facades\DB; use Carbon\Carbon;
class GenerateLessons extends Command {
 protected $signature='chist:generate-lessons {--period=}'; protected $description='Создать календарные занятия из импортированного расписания';
 public function handle(){ $q=DB::table('schedule_entries')->where('active',1);if($this->option('period'))$q->where('academic_period_id',$this->option('period'));$n=0;foreach($q->cursor() as $e){$d=Carbon::parse($e->starts_on);$end=Carbon::parse($e->ends_on);while($d->lte($end)){if($d->isoWeekday()==$e->weekday){DB::table('lesson_occurrences')->updateOrInsert(['schedule_entry_id'=>$e->id,'lesson_date'=>$d->toDateString()],['academic_period_id'=>$e->academic_period_id,'study_group_id'=>$e->study_group_id,'teacher_id'=>$e->teacher_id,'subject_id'=>$e->subject_id,'room_id'=>$e->room_id,'lesson_number'=>$e->lesson_number,'status'=>'planned','updated_at'=>now(),'created_at'=>now()]);$n++;}$d->addDay();}}$this->info("Занятия сформированы: $n");return self::SUCCESS;}
}
