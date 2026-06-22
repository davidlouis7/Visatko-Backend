<?php

namespace Tests\Feature\Blog;

use App\Models\User;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Languages\Models\Language;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BlogFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, LanguageSeeder::class]);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
        Sanctum::actingAs($this->admin);
    }

    public function test_admin_builds_blog_taxonomy_and_published_post_visible_publicly(): void
    {
        $category = $this->postJson('/api/v1/admin/blog/categories', ['is_active' => true, 'translations' => ['en' => ['name' => 'Visa Guides', 'slug' => 'visa-guides']]])->assertCreated()->json('data.id');
        $tag = $this->postJson('/api/v1/admin/blog/tags', ['translations' => ['en' => ['name' => 'Travel', 'slug' => 'travel']]])->assertCreated()->json('data.id');

        $this->postJson('/api/v1/admin/blog/posts', ['category_id' => $category, 'tag_ids' => [$tag], 'is_published' => true, 'is_featured' => true, 'translations' => ['en' => ['title' => 'Visa Application Guide', 'slug' => 'visa-application-guide', 'excerpt' => 'Everything you need.', 'content' => '<p>Complete guide.</p>']]])
            ->assertCreated()->assertJsonPath('data.tags.0.name', 'Travel');

        $this->getJson('/api/v1/blog/posts')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Visa Application Guide');
        $this->getJson('/api/v1/blog/posts/visa-application-guide')->assertOk()->assertJsonPath('data.content', '<p>Complete guide.</p>');
    }

    public function test_drafts_and_future_posts_are_not_public(): void
    {
        $category = BlogCategory::factory()->create();
        $en = Language::query()->where('code', 'en')->firstOrFail();
        $category->translations()->create(['language_id' => $en->id, 'name' => 'News', 'slug' => 'news']);
        foreach ([[false, now()], [true, now()->addDay()]] as [$published, $date]) {
            $post = BlogPost::factory()->create(['author_id' => $this->admin->id, 'category_id' => $category->id, 'is_published' => $published, 'published_at' => $date]);
            $post->translations()->create(['language_id' => $en->id, 'title' => fake()->sentence(), 'slug' => fake()->unique()->slug(), 'content' => 'Hidden']);
        }
        $this->getJson('/api/v1/blog/posts')->assertOk()->assertJsonCount(0, 'data');
    }
}
