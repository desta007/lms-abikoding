<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL doesn't support directly adding values to enum, so we need to modify the column
        DB::statement("ALTER TABLE exam_attempts MODIFY COLUMN status ENUM('in_progress', 'submitted', 'graded', 'passed', 'failed') DEFAULT 'in_progress'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE exam_attempts MODIFY COLUMN status ENUM('in_progress', 'submitted', 'graded') DEFAULT 'in_progress'");
    }
};
