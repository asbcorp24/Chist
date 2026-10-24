<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{
foreach(['upbringing_events','student_achievements','prevention_councils','curator_plans','parent_meetings'] as $name){Schema::table($name,function(Blueprint $t){if(!Schema::hasColumn($t->getTable(),'archived_at'))$t->timestamp('archived_at')->nullable()->index();});}
if(!Schema::hasColumn('student_clubs','active'))Schema::table('student_clubs',function(Blueprint $t){$t->boolean('active')->default(true)->index();});
}public function down():void{foreach(['upbringing_events','student_achievements','prevention_councils','curator_plans','parent_meetings'] as $name){if(Schema::hasColumn($name,'archived_at'))Schema::table($name,function(Blueprint $t){$t->dropColumn('archived_at');});}}};