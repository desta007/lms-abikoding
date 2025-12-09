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
            // Change enum to string to support 'mixed' type and nullable
            $table->string('material_type')->nullable()->change();
            
            // Add fields for multiple content types
            $table->string('video_url')->nullable()->after('file_mime_type');
            $table->string('pdf_file_path')->nullable()->after('video_url');
            $table->bigInteger('pdf_file_size')->nullable()->after('pdf_file_path');
            $table->string('pdf_file_mime_type')->nullable()->after('pdf_file_size');
            $table->string('image_file_path')->nullable()->after('pdf_file_mime_type');
            $table->bigInteger('image_file_size')->nullable()->after('image_file_path');
            $table->string('image_file_mime_type')->nullable()->after('image_file_size');
            $table->text('text_content')->nullable()->after('image_file_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapter_materials', function (Blueprint $table) {
            $table->dropColumn([
                'video_url',
                'pdf_file_path',
                'pdf_file_size',
                'pdf_file_mime_type',
                'image_file_path',
                'image_file_size',
                'image_file_mime_type',
                'text_content'
            ]);
            $table->string('material_type')->nullable(false)->change();
        });
    }
};
