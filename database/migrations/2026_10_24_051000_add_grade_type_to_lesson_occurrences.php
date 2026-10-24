<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::table('lesson_occurrences',function(Blueprint $t){if(!Schema::hasColumn('lesson_occurrences','grade_type'))$t->string('grade_type',30)->default('lesson')->after('lesson_number');});}public function down():void{Schema::table('lesson_occurrences',function(Blueprint $t){if(Schema::hasColumn('lesson_occurrences','grade_type'))$t->dropColumn('grade_type');});}};
