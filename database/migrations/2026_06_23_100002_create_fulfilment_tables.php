<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('consultations')) {
            Schema::rename('consultations', 'consultations_legacy_phase1');
        }

        Schema::create('consultations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('preferred_destination_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('preferred_visa_service_id')->nullable()->constrained('visa_services')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name', 150);
            $table->string('phone', 30);
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('email', 254)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('current_emirate', 100)->nullable();
            $table->boolean('are_you_residing_in_uae');
            $table->string('monthly_salary_range', 30);
            $table->boolean('salary_transferred_regularly')->nullable();
            $table->boolean('has_tenancy_contract')->nullable();
            $table->boolean('owns_car')->nullable();
            $table->boolean('has_previous_travel_history')->nullable();
            $table->boolean('previous_visa_refusal')->nullable();
            $table->date('expected_travel_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 100)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('meta_event_id')->nullable()->index();
            $table->string('status', 40)->default('new')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['assigned_to', 'status']);
        });

        Schema::create('visa_applications', function (Blueprint $table): void {
            $table->id();
            $table->string('application_number', 30)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('visa_service_id')->constrained()->restrictOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name', 150);
            $table->string('email', 254)->nullable();
            $table->string('phone', 30);
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('residence_country', 100)->nullable();
            $table->string('emirate', 100)->nullable();
            $table->string('passport_number')->nullable();
            $table->date('travel_date')->nullable();
            $table->string('status', 40)->default('new')->index();
            $table->string('payment_status', 30)->default('unpaid')->index();
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('source', 100)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['assigned_to', 'status']);
            $table->index(['visa_service_id', 'status']);
        });

        Schema::table('consultations', function (Blueprint $table): void {
            $table->foreignId('converted_application_id')->nullable()->after('assigned_to')->constrained('visa_applications')->nullOnDelete();
        });

        Schema::create('application_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('visa_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('media_id')->constrained('media')->restrictOnDelete();
            $table->string('document_type', 40);
            $table->string('title');
            $table->string('status', 30)->default('uploaded')->index();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::table('consultations', fn (Blueprint $table) => $table->dropConstrainedForeignId('converted_application_id'));
        Schema::dropIfExists('visa_applications');
        Schema::dropIfExists('consultations');
        if (Schema::hasTable('consultations_legacy_phase1')) {
            Schema::rename('consultations_legacy_phase1', 'consultations');
        }
    }
};
