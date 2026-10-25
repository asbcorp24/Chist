<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentMovement extends Model
{
    protected $table = 'student_movements';
    protected $guarded = [];
    protected $casts = ['effective_on' => 'date', 'order_date' => 'date'];
}
