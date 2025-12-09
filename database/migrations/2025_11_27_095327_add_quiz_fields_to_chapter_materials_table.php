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
        Schema::table('chapter_materials', function (Blueprint $table) {
            $table->boolean('has_quiz')->default(false)->after('text_content');
            $table->boolean('quiz_required_for_next')->default(false)->after('has_quiz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapter_materials', function (Blueprint $table) {
            $table->dropColumn(['has_quiz', 'quiz_required_for_next']);
        });
    }
};
