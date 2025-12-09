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
            $table->string('zoom_meeting_id')->nullable()->after('ended_at');
            $table->string('zoom_meeting_password')->nullable()->after('zoom_meeting_id');
            $table->string('zoom_join_url')->nullable()->after('zoom_meeting_password');
            $table->string('zoom_start_url')->nullable()->after('zoom_join_url');
            $table->boolean('is_zoom_meeting')->default(false)->after('zoom_start_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_meeting_id',
                'zoom_meeting_password',
                'zoom_join_url',
                'zoom_start_url',
                'is_zoom_meeting',
            ]);
        });
    }
};
