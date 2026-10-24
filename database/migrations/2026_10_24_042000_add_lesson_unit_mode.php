<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('academic_periods', function (Blueprint $t) {
            $t->string('lesson_unit_mode', 16)->default('lesson')->after('is_closed');
        });
        Schema::table('schedule_entries', function (Blueprint $t) {
            $t->unsignedTinyInteger('source_lesson_number')->nullable()->after('lesson_number');
            $t->unsignedTinyInteger('source_lesson_number_end')->nullable()->after('source_lesson_number');
        });
    }

    public function down(): void {
        Schema::table('schedule_entries', function (Blueprint $t) {
            $t->dropColumn(['source_lesson_number','source_lesson_number_end']);
        });
        Schema::table('academic_periods', function (Blueprint $t) {
            $t->dropColumn('lesson_unit_mode');
        });
    }
};
