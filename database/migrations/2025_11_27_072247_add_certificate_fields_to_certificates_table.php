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
        Schema::table('certificates', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('certificates', 'is_valid')) {
                $table->boolean('is_valid')->default(true)->after('verification_code');
            }
            
            if (!Schema::hasColumn('certificates', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('issued_at');
            }
            
            if (!Schema::hasColumn('certificates', 'revoked_reason')) {
                $table->text('revoked_reason')->nullable()->after('revoked_at');
            }
            
            if (!Schema::hasColumn('certificates', 'metadata')) {
                $table->json('metadata')->nullable()->after('revoked_reason');
            }
            
            // Rename pdf_path to file_path if pdf_path exists
            if (Schema::hasColumn('certificates', 'pdf_path') && !Schema::hasColumn('certificates', 'file_path')) {
                $table->renameColumn('pdf_path', 'file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'is_valid')) {
                $table->dropColumn('is_valid');
            }
            
            if (Schema::hasColumn('certificates', 'revoked_at')) {
                $table->dropColumn('revoked_at');
            }
            
            if (Schema::hasColumn('certificates', 'revoked_reason')) {
                $table->dropColumn('revoked_reason');
            }
            
            if (Schema::hasColumn('certificates', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
