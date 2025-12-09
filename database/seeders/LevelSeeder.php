<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Beginner', 'order' => 1],
            ['name' => 'Intermediate', 'order' => 2],
            ['name' => 'Advanced', 'order' => 3],
        ];

        foreach ($levels as $level) {
            Level::create([
                'name' => $level['name'],
                'slug' => Str::slug($level['name']),
                'order' => $level['order'],
            ]);
        }
    }
}
