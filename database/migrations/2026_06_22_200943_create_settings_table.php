<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group', 100)->default('general');
            $table->string('key', 150);
            $table->longText('value');
            $table->string('type', 20)->default('string');
            $table->boolean('is_public')->default(false)->index();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
