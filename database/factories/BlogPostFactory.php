<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        return ['author_id' => User::factory(), 'category_id' => BlogCategory::factory(), 'is_published' => true, 'is_featured' => false, 'published_at' => now(), 'sort_order' => 0];
    }
}
