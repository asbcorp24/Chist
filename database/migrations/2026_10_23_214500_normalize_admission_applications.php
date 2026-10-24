<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  if (!Schema::hasTable('admission_applications')) {
   Schema::create('admission_applications', function (Blueprint $t) {
    $t->id();
    $t->foreignId('applicant_id')->constrained('admission_applicants')->cascadeOnDelete();
    $t->foreignId('campaign_id')->constrained('admission_campaigns')->cascadeOnDelete();
    $t->foreignId('target_id')->constrained('admission_targets')->cascadeOnDelete();
    $t->unsignedInteger('priority')->default(1);
    $t->decimal('average_score',5,3)->nullable();
    $t->decimal('entrance_score',7,3)->nullable();
    $t->boolean('original_document')->default(false);
    $t->string('status',40)->default('submitted');
    $t->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
    $t->text('note')->nullable();
    $t->timestamps();
    $t->unique(['applicant_id','campaign_id','target_id']);
   });
  }

  foreach (['target_id','campaign_id','target_group_id','student_id','specialty','education_base','average_score','status','priority','original_document','entrance_score'] as $column) {
   if (Schema::hasColumn('admission_applicants',$column)) {
    Schema::table('admission_applicants', fn (Blueprint $t) => $t->dropColumn($column));
   }
  }
 }
 public function down(): void {}
};