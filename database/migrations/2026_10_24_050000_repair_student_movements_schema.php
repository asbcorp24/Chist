<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('student_movements')) return;

        if (!Schema::hasColumn('student_movements','academic_period_id')) {
            Schema::table('student_movements', function (Blueprint $table) {
                $table->unsignedBigInteger('academic_period_id')->nullable()->after('student_id');
                $table->index('academic_period_id','student_movements_academic_period_idx');
            });
        }

        if (!Schema::hasColumn('student_movements','academic_order_id')) {
            Schema::table('student_movements', function (Blueprint $table) {
                $table->unsignedBigInteger('academic_order_id')->nullable()->after('effective_on');
                $table->index('academic_order_id','student_movements_academic_order_idx');
            });
        }
    }

    public function down(): void {
        if (!Schema::hasTable('student_movements')) return;
        Schema::table('student_movements', function (Blueprint $table) {
            if (Schema::hasColumn('student_movements','academic_order_id')) {
                $table->dropIndex('student_movements_academic_order_idx');
                $table->dropColumn('academic_order_id');
            }
            if (Schema::hasColumn('student_movements','academic_period_id')) {
                $table->dropIndex('student_movements_academic_period_idx');
                $table->dropColumn('academic_period_id');
            }
        });
    }
};