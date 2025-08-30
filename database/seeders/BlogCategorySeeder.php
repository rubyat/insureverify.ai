<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'Announcements', 'status' => 1],
            ['title' => 'Guides', 'status' => 1],
            ['title' => 'Product Updates', 'status' => 1],
            ['title' => 'Case Studies', 'status' => 1],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['title']);
            BlogCategory::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $cat['title'],
                    'status' => $cat['status'],
                    'content' => $cat['content'] ?? null,
                ]
            );
        }
    }
}
