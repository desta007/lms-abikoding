<?php

namespace Database\Seeders;

use App\Models\SourceCodeCategory;
use Illuminate\Database\Seeder;

class SourceCodeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Flutter Apps', 'slug' => 'flutter-apps', 'description' => 'Aplikasi mobile menggunakan Flutter framework'],
            ['name' => 'Laravel Projects', 'slug' => 'laravel-projects', 'description' => 'Project web menggunakan Laravel PHP framework'],
            ['name' => 'Vue.js Apps', 'slug' => 'vuejs-apps', 'description' => 'Aplikasi frontend menggunakan Vue.js'],
            ['name' => 'React Native Apps', 'slug' => 'react-native-apps', 'description' => 'Aplikasi mobile menggunakan React Native'],
            ['name' => 'Full Stack Projects', 'slug' => 'full-stack-projects', 'description' => 'Project full stack dengan frontend dan backend'],
            ['name' => 'API Projects', 'slug' => 'api-projects', 'description' => 'REST API dan backend services'],
        ];

        foreach ($categories as $category) {
            SourceCodeCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
