<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Support\Facades\DB;
return new class extends Migration{public function up(){$now=now();$rules=[
['retake_second_days','Срок назначения второй пересдачи','attestation','integer','30','Локальный срок после неуспешной первой пересдачи.'],
['commission_min_members','Минимальный состав комиссии','attestation','integer','3','Локально установленный минимальный состав комиссии; федеральное требование самой комиссии проверяется отдельно.'],
['practice_require_positive_result','Практика должна быть закрыта','practice','boolean','1','Учитывать незакрытую практику как проблему контроля.'],
['gia_require_no_debts','Допуск к ГИА без академических задолженностей','gia','boolean','1','Локальная автоматическая блокировка допуска при наличии задолженностей.'],
['gia_require_practice','Допуск к ГИА после закрытия практики','gia','boolean','1','Локальная автоматическая блокировка допуска при незакрытой практике.'],
['hours_tolerance','Допустимое отклонение нагрузки, часов','workload','decimal','0','Допустимая разница между потребностью и распределением.'],
['teacher_weekly_hours_warning','Предупреждение по недельной нагрузке преподавателя','workload','decimal','36','Контрольный порог для управленческого предупреждения, не федеральная норма.'],
['honors_min_grade5_percent','Минимальная доля оценок 5 для внутренней проверки диплома с отличием, %','graduation','decimal','75','Внутренний предварительный контроль; не заменяет действующие правила выдачи диплома.'],
];foreach($rules as $x)DB::table('academic_rules')->updateOrInsert(['key'=>$x[0]],['name'=>$x[1],'category'=>$x[2],'value_type'=>$x[3],'value'=>$x[4],'description'=>$x[5],'enabled'=>1,'created_at'=>$now,'updated_at'=>$now]);}public function down(){DB::table('academic_rules')->whereIn('key',['retake_second_days','commission_min_members','practice_require_positive_result','gia_require_no_debts','gia_require_practice','hours_tolerance','teacher_weekly_hours_warning','honors_min_grade5_percent'])->delete();}};
