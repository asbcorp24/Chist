<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScholarshipCommission extends Model
{
    protected $table = 'scholarship_commissions';
    protected $guarded = [];
    public function decisions(){ return $this->hasMany(ScholarshipDecision::class, 'scholarship_commission_id'); }
}
