<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
  Schema::create('permissions',function(Blueprint $t){$t->id();$t->string('code')->unique();$t->string('name');$t->string('group')->nullable();$t->timestamps();});
  Schema::create('permission_role',function(Blueprint $t){$t->foreignId('permission_id')->constrained()->cascadeOnDelete();$t->foreignId('role_id')->constrained()->cascadeOnDelete();$t->primary(['permission_id','role_id']);});
  Schema::table('roles',function(Blueprint $t){$t->boolean('is_system')->default(false);$t->unsignedInteger('level')->default(10);});
 }
 public function down(){Schema::dropIfExists('permission_role');Schema::dropIfExists('permissions');Schema::table('roles',function(Blueprint $t){$t->dropColumn(['is_system','level']);});}
};
