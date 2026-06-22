<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('disk', 50);
            $table->string('path', 500)->unique();
            $table->string('original_name', 255);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size');
            $table->string('visibility', 10);
            $table->string('collection', 100)->index();
            $table->nullableMorphs('mediable');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
