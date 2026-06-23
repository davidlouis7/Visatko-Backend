<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.manage',
            'languages.view', 'languages.create', 'languages.update', 'languages.delete',
            'settings.view', 'settings.create', 'settings.update', 'settings.delete',
            'media.view', 'media.upload', 'media.delete',
            'countries.view', 'countries.create', 'countries.update', 'countries.delete',
            'services.view', 'services.create', 'services.update', 'services.delete',
            'blog.view', 'blog.create', 'blog.update', 'blog.delete',
            'pages.view', 'pages.create', 'pages.update', 'pages.delete',
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
            'consultations.view', 'consultations.create', 'consultations.update', 'consultations.assign', 'consultations.convert', 'consultations.delete',
            'applications.view', 'applications.create', 'applications.update', 'applications.assign', 'applications.change_status', 'applications.delete',
            'documents.view', 'documents.upload', 'documents.review', 'documents.delete',
            'notes.view', 'notes.create',
            'follow_ups.view', 'follow_ups.create', 'follow_ups.update', 'follow_ups.complete',
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.issue', 'invoices.send', 'invoices.mark_paid', 'invoices.download',
            'credit_notes.view', 'credit_notes.create', 'credit_notes.issue', 'credit_notes.download',
            'payments.view', 'payments.create', 'payments.review', 'payments.approve_bank_transfers', 'payments.reject_bank_transfers', 'payments.manage_stripe', 'payments.manage_tabby',
            'refunds.view', 'refunds.create', 'refunds.approve', 'refunds.reject', 'refunds.process',
            'emails.templates.view', 'emails.templates.update', 'emails.logs.view',
            'marketing.settings.view', 'marketing.settings.update', 'marketing.events.view',
            'contact_messages.view', 'contact_messages.update', 'contact_messages.assign', 'contact_messages.close',
            'reviews.view', 'reviews.create', 'reviews.update', 'reviews.delete',
            'counters.view', 'counters.create', 'counters.update', 'counters.delete',
            'team.view', 'team.create', 'team.update', 'team.delete',
            'partners.view', 'partners.create', 'partners.update', 'partners.delete',
            'branches.view', 'branches.create', 'branches.update', 'branches.delete',
            'reports.dashboard.view', 'reports.sales.view', 'reports.employee_performance.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $all = Permission::query()->pluck('name')->all();
        $content = array_values(array_filter($all, fn (string $permission): bool => str_starts_with($permission, 'languages.') || str_starts_with($permission, 'settings.') || str_starts_with($permission, 'media.') || str_starts_with($permission, 'countries.') || str_starts_with($permission, 'services.') || str_starts_with($permission, 'blog.') || str_starts_with($permission, 'pages.')));

        Role::findOrCreate('Super Admin', 'web')->syncPermissions($all);
        Role::findOrCreate('Admin', 'web')->syncPermissions(array_values(array_diff($all, ['roles.manage'])));
        $financePermissions = ['invoices.view', 'invoices.create', 'invoices.update', 'invoices.issue', 'invoices.send', 'invoices.mark_paid', 'invoices.download', 'credit_notes.view', 'credit_notes.create', 'credit_notes.issue', 'credit_notes.download', 'payments.view', 'payments.create', 'payments.review', 'payments.approve_bank_transfers', 'payments.reject_bank_transfers', 'payments.manage_stripe', 'payments.manage_tabby', 'refunds.view', 'refunds.create', 'refunds.approve', 'refunds.reject', 'refunds.process', 'settings.view'];
        Role::findOrCreate('Accountant', 'web')->syncPermissions([...$financePermissions, 'reports.dashboard.view', 'reports.sales.view']);
        $consultantPermissions = ['services.view', 'customers.view', 'customers.create', 'customers.update', 'consultations.view', 'consultations.create', 'consultations.update', 'consultations.assign', 'consultations.convert', 'applications.view', 'applications.create', 'applications.update', 'applications.assign', 'applications.change_status', 'documents.view', 'documents.upload', 'documents.review', 'notes.view', 'notes.create', 'follow_ups.view', 'follow_ups.create', 'follow_ups.update', 'follow_ups.complete'];
        Role::findOrCreate('Visa Consultant', 'web')->syncPermissions($consultantPermissions);
        Role::findOrCreate('CRM User', 'web')->syncPermissions(['customers.view', 'customers.create', 'customers.update', 'consultations.view', 'consultations.create', 'consultations.update', 'consultations.assign', 'consultations.convert', 'applications.view', 'applications.create', 'applications.assign', 'documents.view', 'documents.upload', 'notes.view', 'notes.create', 'follow_ups.view', 'follow_ups.create', 'follow_ups.update', 'follow_ups.complete', 'contact_messages.view', 'contact_messages.update']);

        Role::findByName('Admin', 'web')->givePermissionTo($content);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
