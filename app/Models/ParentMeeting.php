<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class ParentMeeting extends Model{protected $guarded=[]; protected static function boot(){parent::boot();static::creating(function($x){$x->created_by=auth()->id();});}protected $table='parent_meetings'; protected static function booted():void{static::addGlobalScope('period',function(Builder $q){$p=request()->attributes->get('academic_period');if($p)$q->where('academic_period_id',$p->id);});}}
