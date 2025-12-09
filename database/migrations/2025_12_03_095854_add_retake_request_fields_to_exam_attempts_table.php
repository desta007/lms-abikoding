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
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->boolean('retake_requested')->default(false)->after('status');
            $table->timestamp('retake_requested_at')->nullable()->after('retake_requested');
            $table->boolean('retake_approved')->default(false)->after('retake_requested_at');
            $table->timestamp('retake_approved_at')->nullable()->after('retake_approved');
            $table->foreignId('retake_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('retake_approved_at');
            $table->text('retake_rejection_reason')->nullable()->after('retake_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropForeign(['retake_approved_by']);
            $table->dropColumn([
                'retake_requested',
                'retake_requested_at',
                'retake_approved',
                'retake_approved_at',
                'retake_approved_by',
                'retake_rejection_reason',
            ]);
        });
    }
};
