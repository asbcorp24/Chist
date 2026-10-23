<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('practice_placements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('academic_period_id')->constrained()->restrictOnDelete();
            $t->foreignId('student_id')->constrained()->restrictOnDelete();
            $t->foreignId('study_group_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $t->string('practice_type')->nullable();
            $t->string('organization_name')->nullable();
            $t->string('organization_address')->nullable();
            $t->string('organization_supervisor')->nullable();
            $t->date('starts_on')->nullable();
            $t->date('ends_on')->nullable();
            $t->unsignedInteger('planned_hours')->nullable();
            $t->string('status')->default('planned');
            $t->string('final_grade')->nullable();
            $t->text('note')->nullable();
            $t->timestamps();
            $t->index(['academic_period_id','student_id']);
        });

        Schema::create('practice_diary_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('practice_placement_id')->constrained()->cascadeOnDelete();
            $t->date('work_date');
            $t->decimal('hours',5,2)->nullable();
            $t->text('work_description');
            $t->string('status')->default('draft');
            $t->text('teacher_comment')->nullable();
            $t->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('checked_at')->nullable();
            $t->timestamps();
            $t->unique(['practice_placement_id','work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_diary_entries');
        Schema::dropIfExists('practice_placements');
    }
};
