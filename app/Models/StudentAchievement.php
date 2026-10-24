<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class StudentAchievement extends Model{protected $guarded=[];protected $table='student_achievements'; protected static function booted():void{static::addGlobalScope('period',function(Builder $q){$p=request()->attributes->get('academic_period');if($p)$q->where('academic_period_id',$p->id);});}}
