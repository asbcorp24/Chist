<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
  Schema::create('academic_years',function(Blueprint $t){$t->id();$t->string('name')->unique();$t->date('starts_on');$t->date('ends_on');$t->boolean('is_current')->default(false);$t->boolean('is_closed')->default(false);$t->timestamps();});
  Schema::create('academic_periods',function(Blueprint $t){$t->id();$t->foreignId('academic_year_id')->constrained()->restrictOnDelete();$t->string('name');$t->unsignedTinyInteger('number');$t->date('starts_on');$t->date('ends_on');$t->boolean('is_current')->default(false);$t->boolean('is_closed')->default(false);$t->timestamps();$t->unique(['academic_year_id','number']);});
  Schema::create('roles',function(Blueprint $t){$t->id();$t->string('code')->unique();$t->string('name');$t->timestamps();});
  Schema::create('rooms',function(Blueprint $t){$t->id();$t->integer('source_id')->nullable()->unique();$t->string('name');$t->unsignedInteger('capacity')->nullable();$t->string('building')->nullable();$t->timestamps();});
  Schema::create('teachers',function(Blueprint $t){$t->id();$t->integer('source_id')->nullable()->unique();$t->string('last_name');$t->string('first_name')->nullable();$t->string('middle_name')->nullable();$t->foreignId('main_room_id')->nullable()->constrained('rooms')->nullOnDelete();$t->boolean('active')->default(true);$t->timestamps();});
  Schema::create('specialties',function(Blueprint $t){$t->id();$t->integer('source_id')->nullable()->unique();$t->string('code')->nullable();$t->string('name');$t->timestamps();});
  Schema::create('study_groups',function(Blueprint $t){$t->id();$t->integer('source_id')->nullable()->unique();$t->string('name')->index();$t->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete();$t->foreignId('curator_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();$t->unsignedInteger('expected_students')->nullable();$t->boolean('active')->default(true);$t->timestamps();});
  Schema::create('students',function(Blueprint $t){$t->id();$t->foreignId('current_group_id')->nullable()->constrained('study_groups')->nullOnDelete();$t->string('last_name');$t->string('first_name');$t->string('middle_name')->nullable();$t->string('status')->default('active');$t->date('enrolled_on')->nullable();$t->date('left_on')->nullable();$t->timestamps();});
  Schema::create('student_group_memberships',function(Blueprint $t){$t->id();$t->foreignId('student_id')->constrained()->restrictOnDelete();$t->foreignId('study_group_id')->constrained()->restrictOnDelete();$t->foreignId('academic_period_id')->constrained()->restrictOnDelete();$t->date('starts_on')->nullable();$t->date('ends_on')->nullable();$t->string('status')->default('active');$t->timestamps();$t->unique(['student_id','academic_period_id']);});
  Schema::create('subjects',function(Blueprint $t){$t->id();$t->integer('source_id')->nullable()->unique();$t->string('short_name')->nullable();$t->string('name');$t->timestamps();});
  Schema::create('study_types',function(Blueprint $t){$t->id();$t->integer('source_id')->nullable()->unique();$t->string('name');$t->timestamps();});
 }
 public function down(){foreach(['study_types','subjects','student_group_memberships','students','study_groups','specialties','teachers','rooms','roles','academic_periods','academic_years'] as $x)Schema::dropIfExists($x);}
};
