<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table): void {
            $table->id();
            $table->char('code', 2)->unique();
            $table->foreignId('flag_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('country_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180);
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->unique(['country_id', 'language_id']);
            $table->unique(['language_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_translations');
        Schema::dropIfExists('countries');
    }
};
