<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LessonOccurrenceGenerator
{
    public function generate(int $periodId): int
    {
        $created=0; $updated=0; $cancelled=0; $protected=0;
        $holidays=DB::table('calendar_exceptions')->where('academic_period_id',$periodId)
            ->where('type','holiday')->pluck('exception_date')->map(fn($x)=>(string)$x)->flip();

        $expected=[];
        foreach(DB::table('schedule_entries')->where('academic_period_id',$periodId)->where('active',1)->cursor() as $e){
            $d=Carbon::parse($e->starts_on); $end=Carbon::parse($e->ends_on);
            while($d->lte($end)){
                if($d->isoWeekday()==$e->weekday && !isset($holidays[$d->toDateString()])){
                    $date=$d->toDateString();
                    $key=$this->key($date,$e->study_group_id,$e->subgroup,$e->lesson_number);
                    $expected[$key]=[$e,$date];
                }
                $d->addDay();
            }
        }

        foreach($expected as [$e,$date]){
            $q=DB::table('lesson_occurrences')->where('academic_period_id',$periodId)
                ->whereDate('lesson_date',$date)->where('study_group_id',$e->study_group_id)
                ->where('lesson_number',$e->lesson_number);
            $e->subgroup===null ? $q->whereNull('subgroup') : $q->where('subgroup',$e->subgroup);
            $lesson=$q->orderByDesc('id')->first();
            $data=[
                'schedule_entry_id'=>$e->id,'academic_period_id'=>$e->academic_period_id,
                'study_group_id'=>$e->study_group_id,'subgroup'=>$e->subgroup,'teacher_id'=>$e->teacher_id,
                'subject_id'=>$e->subject_id,'room_id'=>$e->room_id,'lesson_number'=>$e->lesson_number,
                'status'=>'planned','updated_at'=>now()
            ];
            if(!$lesson){
                DB::table('lesson_occurrences')->insert($data+['lesson_date'=>$date,'created_at'=>now()]);
                $created++; continue;
            }
            if($this->hasJournalData($lesson)){
                $protected++; continue;
            }
            DB::table('lesson_occurrences')->where('id',$lesson->id)->update($data);
            $updated++;
        }

        // Rector is authoritative only for still-editable schedule slots. A lesson with journal
        // data is historical evidence and is never cancelled by a later weekly import.
        foreach(DB::table('lesson_occurrences')->where('academic_period_id',$periodId)
            ->whereDate('lesson_date','>=',now()->toDateString())->where('status','planned')->cursor() as $lesson){
            $key=$this->key((string)$lesson->lesson_date,$lesson->study_group_id,$lesson->subgroup,$lesson->lesson_number);
            if(isset($expected[$key])) continue;
            if($this->hasJournalData($lesson)){ $protected++; continue; }
            DB::table('lesson_occurrences')->where('id',$lesson->id)->update(['status'=>'cancelled','updated_at'=>now()]);
            $cancelled++;
        }

        return $created+$updated;
    }

    private function key(string $date,int $groupId,$subgroup,int $lessonNumber): string
    {
        return $date.'|'.$groupId.'|'.($subgroup===null?'':$subgroup).'|'.$lessonNumber;
    }

    private function hasJournalData(object $lesson): bool
    {
        if(trim((string)($lesson->topic??''))!=='' || trim((string)($lesson->homework??''))!=='') return true;
        foreach(['attendance_records','grade_records','homework_submissions'] as $table){
            if(DB::table($table)->where('lesson_occurrence_id',$lesson->id)->exists()) return true;
        }
        return false;
    }
}
