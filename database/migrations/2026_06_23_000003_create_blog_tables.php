<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('blog_category_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180);
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->unique(['blog_category_id', 'language_id'], 'blog_category_language_unique');
            $table->unique(['language_id', 'slug'], 'blog_category_slug_unique');
        });
        Schema::create('blog_tags', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('blog_tag_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180);
            $table->timestamps();
            $table->unique(['blog_tag_id', 'language_id']);
            $table->unique(['language_id', 'slug']);
        });
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('blog_categories')->restrictOnDelete();
            $table->foreignId('thumbnail_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('banner_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('blog_post_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220);
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->unique(['blog_post_id', 'language_id']);
            $table->unique(['language_id', 'slug']);
        });
        Schema::create('blog_post_tag', function (Blueprint $table): void {
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_post_translations');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tag_translations');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_category_translations');
        Schema::dropIfExists('blog_categories');
    }
};
