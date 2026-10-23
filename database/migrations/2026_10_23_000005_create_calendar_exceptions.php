<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){Schema::create('calendar_exceptions',function(Blueprint $t){$t->id();$t->foreignId('academic_period_id')->constrained()->cascadeOnDelete();$t->date('exception_date');$t->string('type',30);$t->string('title')->nullable();$t->integer('source_id')->nullable();$t->timestamps();$t->unique(['academic_period_id','exception_date','type']);});Schema::create('schedule_replacements',function(Blueprint $t){$t->id();$t->foreignId('academic_period_id')->constrained()->cascadeOnDelete();$t->date('replacement_date')->nullable();$t->integer('source_id')->nullable();$t->json('payload');$t->timestamps();});}
 public function down(){Schema::dropIfExists('schedule_replacements');Schema::dropIfExists('calendar_exceptions');}
};
