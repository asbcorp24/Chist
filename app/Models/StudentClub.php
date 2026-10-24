<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class StudentClub extends Model{protected $guarded=[]; protected static function boot(){parent::boot();static::creating(function($x){$x->leader_user_id=auth()->id();$x->active=1;});}protected $table='student_clubs'; protected static function booted():void{static::addGlobalScope('period',function(Builder $q){$p=request()->attributes->get('academic_period');if($p)$q->where('academic_period_id',$p->id);});}public function members(){return $this->hasMany(StudentClubMember::class,'student_club_id');}}