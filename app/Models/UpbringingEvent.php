<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class UpbringingEvent extends Model{protected $guarded=[]; protected static function boot(){parent::boot();static::creating(function($x){$x->responsible_user_id=auth()->id();$x->status=$x->status?:'planned';});}protected $table='upbringing_events'; protected static function booted():void{static::addGlobalScope('period',function(Builder $q){$p=request()->attributes->get('academic_period');if($p)$q->where('academic_period_id',$p->id);});}}
