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
            if (!Schema::hasColumn('chapter_materials', 'video_file_path')) {
                $table->string('video_file_path')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('chapter_materials', 'video_file_size')) {
                $table->bigInteger('video_file_size')->nullable()->after('video_file_path');
            }
            if (!Schema::hasColumn('chapter_materials', 'video_file_mime_type')) {
                $table->string('video_file_mime_type')->nullable()->after('video_file_size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapter_materials', function (Blueprint $table) {
            if (Schema::hasColumn('chapter_materials', 'video_file_path')) {
                $table->dropColumn('video_file_path');
            }
            if (Schema::hasColumn('chapter_materials', 'video_file_size')) {
                $table->dropColumn('video_file_size');
            }
            if (Schema::hasColumn('chapter_materials', 'video_file_mime_type')) {
                $table->dropColumn('video_file_mime_type');
            }
        });
    }
};
