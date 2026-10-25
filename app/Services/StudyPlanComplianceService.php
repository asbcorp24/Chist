<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
class StudyPlanComplianceService{
 public function rows(object $curriculum, object $period, $items){
  $out=collect();$groups=DB::table('study_groups')->where(['specialty_id'=>$curriculum->specialty_id,'active'=>1])->get();
  foreach($items->where('semester',$period->number) as $i)foreach($groups as $g){
   $rq=DB::table('load_requirements')->where(['academic_period_id'=>$period->id,'study_group_id'=>$g->id,'subject_id'=>$i->subject_id]);
   $lq=DB::table('teaching_load_plans')->where(['academic_period_id'=>$period->id,'study_group_id'=>$g->id,'subject_id'=>$i->subject_id,'status'=>'active']);
   if($i->study_type_id){$rq->where('study_type_id',$i->study_type_id);$lq->where('study_type_id',$i->study_type_id);}
   $required=(float)$rq->sum('required_hours');$allocated=(float)$lq->sum('hours_total');
   $occ=DB::table('lesson_occurrences')->where(['academic_period_id'=>$period->id,'study_group_id'=>$g->id,'subject_id'=>$i->subject_id])->where('status','!=','cancelled')->get();
   $scheduled=0.0;$journalled=0.0;
   foreach($occ as $o){$tm=DB::table('lesson_times')->where(['academic_period_id'=>$period->id,'lesson_number'=>$o->lesson_number])->first();$h=$tm?max(0,(strtotime($tm->ends_at)-strtotime($tm->starts_at))/3600):0;$scheduled+=$h;$hasJournal=DB::table('attendance_records')->where('lesson_occurrence_id',$o->id)->exists()||DB::table('grade_records')->where('lesson_occurrence_id',$o->id)->exists()||trim((string)($o->topic??''))!=='';if($hasJournal)$journalled+=$h;}
   $plan=(float)$i->hours_total;$status='ok';
   if(abs($required-$plan)>.01)$status='requirement';
   elseif(abs($allocated-$plan)>.01)$status='allocation';
   elseif($scheduled+0.01<$plan)$status='schedule';
   $out->push((object)['group_name'=>$g->name,'component_code'=>$i->component_code,'subject_name'=>$i->subject_name,'plan'=>$plan,'required'=>$required,'allocated'=>$allocated,'scheduled'=>round($scheduled,2),'done'=>round($journalled,2),'status'=>$status]);
  }
  return $out;
 }
}