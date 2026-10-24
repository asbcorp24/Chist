<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{
 public function up():void{
  Schema::table('admission_applicants',function(Blueprint $t){$t->string('portal_token',64)->nullable()->unique();$t->string('portal_status',30)->default('draft');$t->timestamp('personal_data_consent_at')->nullable();$t->timestamp('submitted_at')->nullable();$t->timestamp('verified_at')->nullable();});
  Schema::create('admission_documents',function(Blueprint $t){$t->id();$t->foreignId('applicant_id')->constrained('admission_applicants')->cascadeOnDelete();$t->string('document_type',100);$t->string('series',50)->nullable();$t->string('number',100)->nullable();$t->date('issued_on')->nullable();$t->string('issued_by',500)->nullable();$t->string('file_path')->nullable();$t->string('status',30)->default('submitted');$t->text('moderation_note')->nullable();$t->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamp('moderated_at')->nullable();$t->timestamps();$t->index(['applicant_id','status']);});
  Schema::create('admission_messages',function(Blueprint $t){$t->id();$t->foreignId('applicant_id')->constrained('admission_applicants')->cascadeOnDelete();$t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();$t->string('direction',20);$t->text('message');$t->timestamp('read_at')->nullable();$t->timestamps();});
 }
 public function down():void{Schema::dropIfExists('admission_messages');Schema::dropIfExists('admission_documents');Schema::table('admission_applicants',function(Blueprint $t){$t->dropUnique(['portal_token']);$t->dropColumn(['portal_token','portal_status','personal_data_consent_at','submitted_at','verified_at']);});}
};