<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
 public function up():void{
  Schema::table('specialty_curriculum_items',function(Blueprint $t){
   $t->unsignedTinyInteger('course')->nullable()->after('semester');
   $t->string('component_type',30)->default('discipline')->after('course');
   $t->string('component_code',50)->nullable()->after('component_type');
   $t->string('cycle_name',255)->nullable()->after('component_code');
   $t->decimal('lecture_hours',8,2)->default(0)->after('hours_total');
   $t->decimal('practice_hours',8,2)->default(0)->after('lecture_hours');
   $t->decimal('lab_hours',8,2)->default(0)->after('practice_hours');
   $t->decimal('self_study_hours',8,2)->default(0)->after('lab_hours');
   $t->boolean('has_exam')->default(false)->after('control_type');
   $t->boolean('has_credit')->default(false)->after('has_exam');
   $t->boolean('has_differentiated_credit')->default(false)->after('has_credit');
   $t->index(['specialty_curriculum_id','course','semester'],'sci_plan_course_sem_idx');
  });
 }
 public function down():void{
  Schema::table('specialty_curriculum_items',function(Blueprint $t){
   $t->dropIndex('sci_plan_course_sem_idx');
   $t->dropColumn(['course','component_type','component_code','cycle_name','lecture_hours','practice_hours','lab_hours','self_study_hours','has_exam','has_credit','has_differentiated_credit']);
  });
 }
};