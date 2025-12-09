<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->boolean('is_instructor_approved')->default(false)->after('last_position');
            $table->timestamp('approved_at')->nullable()->after('is_instructor_approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            $table->enum('completion_method', ['manual', 'quiz_passed', 'instructor_approved'])->default('manual')->after('approved_by');
            $table->foreignId('quiz_attempt_id')->nullable()->constrained('exam_attempts')->onDelete('set null')->after('completion_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['quiz_attempt_id']);
            $table->dropColumn([
                'is_instructor_approved',
                'approved_at',
                'approved_by',
                'completion_method',
                'quiz_attempt_id',
            ]);
        });
    }
};
