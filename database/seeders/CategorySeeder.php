<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobile Flutter Development',
                'description' => 'Pelajari pengembangan aplikasi mobile menggunakan Flutter framework untuk iOS dan Android',
            ],
            [
                'name' => 'Web Development',
                'description' => 'Pelajari pengembangan aplikasi web modern menggunakan teknologi terbaru',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
