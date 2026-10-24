<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class StudentMeritAction extends Model{
protected $table='student_merit_actions';protected $guarded=[];
public function student(){return $this->belongsTo(Student::class);}
protected static function booted():void{static::addGlobalScope('period',function(Builder $q){$p=request()->attributes->get('academic_period');if($p)$q->where('academic_period_id',$p->id);});static::creating(function(self $x){$p=request()->attributes->get('academic_period');if($p)$x->academic_period_id=$p->id;if(auth()->check())$x->created_by=auth()->id();$x->status='active';});}}
