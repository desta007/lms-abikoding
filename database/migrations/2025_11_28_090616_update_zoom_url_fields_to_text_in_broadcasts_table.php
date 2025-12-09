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
        Schema::table('broadcasts', function (Blueprint $table) {
            // Change zoom_join_url and zoom_start_url from VARCHAR(255) to TEXT
            // because Zoom URLs can be very long (especially start_url with JWT tokens)
            $table->text('zoom_join_url')->nullable()->change();
            $table->text('zoom_start_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            // Revert back to VARCHAR(255)
            $table->string('zoom_join_url')->nullable()->change();
            $table->string('zoom_start_url')->nullable()->change();
        });
    }
};
