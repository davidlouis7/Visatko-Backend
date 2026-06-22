<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name', 150)->index();
            $table->string('email', 254)->nullable()->unique();
            $table->string('phone', 30)->index();
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('residence_country', 100)->nullable();
            $table->string('emirate', 100)->nullable();
            $table->string('preferred_language', 10)->default('en');
            $table->string('source', 100)->nullable()->index();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
