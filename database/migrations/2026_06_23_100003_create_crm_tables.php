<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timelines', function (Blueprint $table): void {
            $table->id();
            $table->morphs('subject');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 60)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::create('notes', function (Blueprint $table): void {
            $table->id();
            $table->morphs('subject');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('note');
            $table->boolean('is_private')->default(true);
            $table->timestamps();
        });
        Schema::create('follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->morphs('subject');
            $table->foreignId('assigned_to')->constrained('users')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('due_at')->index();
            $table->string('status', 30)->default('pending')->index();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['assigned_to', 'status', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('timelines');
    }
};
