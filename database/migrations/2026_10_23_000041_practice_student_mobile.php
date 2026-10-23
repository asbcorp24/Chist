<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up(){
Schema::table('practice_diary_entries',function(Blueprint $t){$t->decimal('latitude',10,7)->nullable();$t->decimal('longitude',10,7)->nullable();$t->decimal('location_accuracy',8,2)->nullable();$t->dateTime('submitted_at')->nullable();});
Schema::create('practice_student_notifications',function(Blueprint $t){$t->id();$t->foreignId('practice_placement_id')->constrained()->cascadeOnDelete();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->string('type');$t->string('fingerprint')->unique();$t->string('title');$t->text('message')->nullable();$t->dateTime('read_at')->nullable();$t->timestamps();});
}public function down(){Schema::dropIfExists('practice_student_notifications');Schema::table('practice_diary_entries',function(Blueprint $t){$t->dropColumn(['latitude','longitude','location_accuracy','submitted_at']);});}};
