<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->string('locale', 10)->default('en')->index();
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('email_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('template_key')->nullable()->index();
            $table->string('subject');
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('status', 30)->default('queued')->index();
            $table->nullableMorphs('related');
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_name', 80)->index();
            $table->string('event_id')->nullable()->unique();
            $table->string('status', 30)->default('pending')->index();
            $table->nullableMorphs('related');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('email_hash')->nullable();
            $table->string('phone_hash')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('fbc')->nullable();
            $table->string('fbp')->nullable();
            $table->text('source_url')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name', 150);
            $table->string('email', 254)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status', 30)->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 100)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->text('landing_page')->nullable();
            $table->text('referrer')->nullable();
            $table->string('gclid')->nullable();
            $table->string('fbclid')->nullable();
            $table->string('meta_event_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_country')->nullable();
            $table->foreignId('visa_service_id')->nullable()->constrained('visa_services')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('review_text');
            $table->foreignId('customer_image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('counters', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->integer('value')->default(0);
            $table->string('suffix', 20)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('team_members', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('job_title');
            $table->foreignId('image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('partners', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name');
            $table->foreignId('logo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->text('website_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->json('phone_numbers')->nullable();
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('google_maps_url')->nullable();
            $table->text('working_hours')->nullable();
            $table->string('emirate')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('visa_applications', function (Blueprint $table): void {
            $table->string('utm_source')->nullable()->after('source');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('utm_content')->nullable()->after('utm_campaign');
            $table->string('utm_term')->nullable()->after('utm_content');
            $table->text('landing_page')->nullable()->after('utm_term');
            $table->text('referrer')->nullable()->after('landing_page');
            $table->string('gclid')->nullable()->after('referrer');
            $table->string('fbclid')->nullable()->after('gclid');
            $table->string('meta_event_id')->nullable()->after('fbclid')->index();
        });
    }

    public function down(): void
    {
        Schema::table('visa_applications', function (Blueprint $table): void {
            $table->dropColumn(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'landing_page', 'referrer', 'gclid', 'fbclid', 'meta_event_id']);
        });
        Schema::dropIfExists('branches');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('counters');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('marketing_events');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
    }
};
