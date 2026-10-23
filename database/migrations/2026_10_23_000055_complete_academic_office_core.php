<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
 public function up():void{
  if(!Schema::hasTable('student_activities')) Schema::create('student_activities',function(Blueprint $t){$t->id();$t->foreignId('academic_period_id')->constrained()->restrictOnDelete();$t->foreignId('study_group_id')->nullable()->constrained()->nullOnDelete();$t->string('title');$t->text('description')->nullable();$t->string('location')->nullable();$t->dateTime('starts_at');$t->dateTime('registration_until')->nullable();$t->unsignedInteger('capacity')->nullable();$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();});
  if(!Schema::hasTable('student_activity_registrations')) Schema::create('student_activity_registrations',function(Blueprint $t){$t->id();$t->foreignId('student_activity_id')->constrained()->cascadeOnDelete();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->string('status')->default('registered');$t->timestamps();$t->unique(['student_activity_id','student_id']);});
  if(!Schema::hasTable('academic_orders')) Schema::create('academic_orders',function(Blueprint $t){$t->id();$t->foreignId('academic_period_id')->constrained()->restrictOnDelete();$t->string('number');$t->date('order_date');$t->foreignId('student_id')->nullable()->constrained()->nullOnDelete();$t->string('type');$t->string('title',500);$t->text('basis')->nullable();$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->unique(['academic_period_id','number']);});
 }
 public function down():void{Schema::dropIfExists('student_activity_registrations');Schema::dropIfExists('student_activities');Schema::dropIfExists('academic_orders');}
};