<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use App\Modules\Languages\Models\Language;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $language = Language::query()->where('code', 'en')->firstOrFail();
        $author = User::query()->first() ?? User::factory()->create();
        $category = BlogCategory::factory()->create();
        $category->translations()->create(['language_id' => $language->id, 'name' => 'Visa Guides', 'slug' => 'visa-guides']);
        $tag = BlogTag::factory()->create();
        $tag->translations()->create(['language_id' => $language->id, 'name' => 'Travel', 'slug' => 'travel']);
        $post = BlogPost::factory()->create(['author_id' => $author->id, 'category_id' => $category->id]);
        $post->translations()->create(['language_id' => $language->id, 'title' => 'Visa Application Guide', 'slug' => 'visa-application-guide', 'content' => 'A practical visa application guide.']);
        $post->tags()->attach($tag);
    }
}
