<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'title' => 'Welcome to InsureVerify AI',
                'category' => 'Announcements',
                'status' => 1,
                'author' => 'Admin',
                'tags' => ['intro', 'platform'],
            ],
            [
                'title' => 'Getting Started Guide',
                'category' => 'Guides',
                'status' => 1,
                'author' => 'Team',
                'tags' => ['guide', 'setup'],
            ],
            [
                'title' => 'August Product Updates',
                'category' => 'Product Updates',
                'status' => 1,
                'author' => 'Product',
                'tags' => ['release', 'changelog'],
            ],
            [
                'title' => 'How ACME reduced fraud by 42%',
                'category' => 'Case Studies',
                'status' => 1,
                'author' => 'Research',
                'tags' => ['case-study', 'fraud'],
            ],
        ];

        foreach ($data as $item) {
            $category = BlogCategory::query()->where('slug', Str::slug($item['category']))->first();
            if (! $category) {
                $category = BlogCategory::query()->create([
                    'title' => $item['category'],
                    'slug' => Str::slug($item['category']),
                    'status' => 1,
                ]);
            }

            $slug = Str::slug($item['title']);
            Blog::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $item['title'],
                    'status' => $item['status'],
                    'content' => $item['content'] ?? '<p>Sample content for ' . e($item['title']) . '.</p>',
                    'template' => $item['template'] ?? [],
                    'blog_category_id' => $category->id,
                    'author' => $item['author'],
                    'publish_date' => Carbon::now()->subDays(rand(0, 30)),
                    'tags' => $item['tags'],
                    'image' => $item['image'] ?? null,
                ]
            );
        }
    }
}
