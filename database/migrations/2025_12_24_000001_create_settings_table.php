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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('text'); // text, textarea, number, boolean, json
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default bank account settings
        $bankSettings = [
            [
                'key' => 'bank_name',
                'value' => 'Bank BCA',
                'group' => 'bank_account',
                'type' => 'text',
                'label' => 'Nama Bank',
                'description' => 'Nama bank untuk transfer pembayaran',
            ],
            [
                'key' => 'bank_account_number',
                'value' => '1234567890',
                'group' => 'bank_account',
                'type' => 'text',
                'label' => 'Nomor Rekening',
                'description' => 'Nomor rekening bank',
            ],
            [
                'key' => 'bank_account_name',
                'value' => 'PT LMS Indonesia',
                'group' => 'bank_account',
                'type' => 'text',
                'label' => 'Atas Nama',
                'description' => 'Nama pemilik rekening bank',
            ],
        ];

        foreach ($bankSettings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
