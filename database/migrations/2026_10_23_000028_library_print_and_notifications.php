<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up(){Schema::table('library_loans',function(Blueprint $t){$t->dateTime('reminded_at')->nullable();$t->dateTime('overdue_notified_at')->nullable();});}public function down(){Schema::table('library_loans',fn(Blueprint $t)=>$t->dropColumn(['reminded_at','overdue_notified_at']));}};
