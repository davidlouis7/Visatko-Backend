<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->index(['status', 'type', 'paid_at'], 'payment_transactions_status_type_paid_at_idx');
            $table->index(['provider', 'status'], 'payment_transactions_provider_status_idx');
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->index(['status', 'payment_status', 'created_at'], 'invoices_status_payment_created_idx');
        });

        Schema::table('marketing_events', function (Blueprint $table): void {
            $table->index(['status', 'event_name', 'created_at'], 'marketing_events_status_name_created_idx');
        });

        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'contact_messages_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', fn (Blueprint $table) => $table->dropIndex('contact_messages_status_created_idx'));
        Schema::table('marketing_events', fn (Blueprint $table) => $table->dropIndex('marketing_events_status_name_created_idx'));
        Schema::table('invoices', fn (Blueprint $table) => $table->dropIndex('invoices_status_payment_created_idx'));
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->dropIndex('payment_transactions_status_type_paid_at_idx');
            $table->dropIndex('payment_transactions_provider_status_idx');
        });
    }
};
