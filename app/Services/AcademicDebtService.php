<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
class AcademicDebtService{
 public function unresolvedForStudent(int $periodId,int $studentId){
  $groups=DB::table('attestation_attempts as x')->join('attestation_sheets as sh','sh.id','=','x.attestation_sheet_id')->where(['sh.academic_period_id'=>$periodId,'x.student_id'=>$studentId])->select('x.*','sh.assessment_type','sh.number as sheet_number','sh.subject_id')->orderBy('x.attempt_no')->get()->groupBy('attestation_sheet_id');
  return $groups->filter(function($attempts){$completed=$attempts->whereNotNull('completed_at')->sortBy('attempt_no');if(!$completed->count())return false;$lastCompleted=$completed->last();return !(bool)$lastCompleted->passed;})->map(fn($a)=>$a->sortBy('attempt_no')->last())->values();
 }
 public function count(int $periodId,int $studentId):int{return $this->unresolvedForStudent($periodId,$studentId)->count();}
}