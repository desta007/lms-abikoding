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
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('minimum_passing_score')->default(70)->after('duration_minutes');
            $table->boolean('is_required_for_progression')->default(false)->after('minimum_passing_score');
            $table->foreignId('chapter_material_id')->nullable()->constrained('chapter_materials')->onDelete('cascade')->after('is_required_for_progression');
            $table->boolean('auto_complete_on_pass')->default(true)->after('chapter_material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['chapter_material_id']);
            $table->dropColumn([
                'minimum_passing_score',
                'is_required_for_progression',
                'chapter_material_id',
                'auto_complete_on_pass',
            ]);
        });
    }
};
