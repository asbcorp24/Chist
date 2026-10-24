<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{
 public function up():void{
  Schema::create('admission_document_requirements',function(Blueprint $t){$t->id();$t->foreignId('campaign_id')->constrained('admission_campaigns')->cascadeOnDelete();$t->string('code',80);$t->string('name');$t->boolean('required')->default(true);$t->boolean('file_required')->default(true);$t->unsignedInteger('sort_order')->default(0);$t->boolean('active')->default(true);$t->timestamps();$t->unique(['campaign_id','code'],'admission_doc_req_code_uq');});
  Schema::table('admission_documents',function(Blueprint $t){$t->foreignId('requirement_id')->nullable()->after('applicant_id')->constrained('admission_document_requirements')->nullOnDelete();});
 }
 public function down():void{Schema::table('admission_documents',function(Blueprint $t){$t->dropConstrainedForeignId('requirement_id');});Schema::dropIfExists('admission_document_requirements');}
};