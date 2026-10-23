<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
class PeriodGuard {public static function writable(int $periodId): void {$closed=(bool)DB::table('academic_periods')->where('id',$periodId)->value('is_closed');abort_if($closed,423,'Учебный период закрыт. Данные доступны только для просмотра.');}}
