<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up(){Schema::table('lesson_occurrences',function(Blueprint $t){$t->text('change_note')->nullable();});}public function down(){Schema::table('lesson_occurrences',function(Blueprint $t){$t->dropColumn('change_note');});}};
