<?php

namespace App\Modules\Finance\Services;

use App\Modules\Settings\Models\Setting;

class FinanceSettings
{
    /** @var array<string, mixed> */
    private array $defaults = [
        'vat_enabled' => true,
        'vat_rate' => 5.0,
        'company_trn' => null,
        'company_legal_name' => 'Visatko Visa Service',
        'invoice_prefix' => 'INV',
        'credit_note_prefix' => 'CN',
        'refund_prefix' => 'RF',
        'invoice_due_days' => 7,
        'invoice_footer_note' => 'Thank you for choosing Visatko Visa Service.',
        'bank_account_name' => null,
        'bank_name' => null,
        'iban' => null,
        'swift_code' => null,
        'bank_transfer_instructions' => 'Please include your invoice number in the transfer reference.',
        'stripe_enabled' => false,
        'tabby_enabled' => false,
        'bank_transfer_enabled' => true,
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('group', 'finance')->where('key', $key)->first();

        return $setting ? $setting->resolvedValue() : ($default ?? $this->defaults[$key] ?? null);
    }

    public function bool(string $key): bool
    {
        return (bool) $this->get($key);
    }

    public function float(string $key): float
    {
        return (float) $this->get($key);
    }

    public function int(string $key): int
    {
        return (int) $this->get($key);
    }

    /** @return array<string, mixed> */
    public function publicPaymentConfig(): array
    {
        return [
            'company_legal_name' => $this->get('company_legal_name'),
            'company_trn' => $this->get('company_trn'),
            'bank_transfer_enabled' => $this->bool('bank_transfer_enabled'),
            'stripe_enabled' => $this->bool('stripe_enabled'),
            'tabby_enabled' => $this->bool('tabby_enabled'),
            'bank_transfer' => [
                'account_name' => $this->get('bank_account_name'),
                'bank_name' => $this->get('bank_name'),
                'iban' => $this->get('iban'),
                'swift_code' => $this->get('swift_code'),
                'instructions' => $this->get('bank_transfer_instructions'),
            ],
        ];
    }
}
