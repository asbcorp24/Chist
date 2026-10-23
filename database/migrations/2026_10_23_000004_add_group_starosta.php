<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){Schema::table('study_groups',function(Blueprint $t){$t->foreignId('starosta_student_id')->nullable()->after('curator_teacher_id');});}
 public function down(){Schema::table('study_groups',function(Blueprint $t){$t->dropColumn('starosta_student_id');});}
};
