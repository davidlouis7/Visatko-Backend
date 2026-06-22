<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('country_id')->constrained()->restrictOnDelete();
            $table->foreignId('thumbnail_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('banner_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->char('currency', 3)->default('AED');
            $table->string('processing_time')->nullable();
            $table->string('visa_validity')->nullable();
            $table->string('stay_duration')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('visa_service_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('visa_service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200);
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('required_documents')->nullable();
            $table->longText('terms_conditions')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->unique(['visa_service_id', 'language_id']);
            $table->unique(['language_id', 'slug']);
        });
        Schema::create('visa_service_media', function (Blueprint $table): void {
            $table->foreignId('visa_service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['visa_service_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_service_media');
        Schema::dropIfExists('visa_service_translations');
        Schema::dropIfExists('visa_services');
    }
};
