<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PreventionCouncil extends Model
{
    protected $table = 'prevention_councils';
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope('active_period', function (Builder $query) {
            $period = request()->attributes->get('academic_period');
            if ($period) {
                $query->where('academic_period_id', $period->id);
            }
            $query->whereNull('archived_at');
        });

        static::creating(function (self $record) {
            $period = request()->attributes->get('academic_period');
            if ($period) $record->academic_period_id = $period->id;
            if (auth()->check()) $record->created_by = auth()->id();
        });
    }
}
