<?php

namespace Database\Seeders;

use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class FinanceSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'vat_enabled', 'value' => true, 'type' => 'boolean'],
            ['key' => 'vat_rate', 'value' => 5, 'type' => 'decimal'],
            ['key' => 'company_trn', 'value' => null, 'type' => 'string'],
            ['key' => 'company_legal_name', 'value' => 'Visatko Visa Service', 'type' => 'string'],
            ['key' => 'invoice_prefix', 'value' => 'INV', 'type' => 'string'],
            ['key' => 'credit_note_prefix', 'value' => 'CN', 'type' => 'string'],
            ['key' => 'refund_prefix', 'value' => 'RF', 'type' => 'string'],
            ['key' => 'invoice_due_days', 'value' => 7, 'type' => 'integer'],
            ['key' => 'invoice_footer_note', 'value' => 'Thank you for choosing Visatko Visa Service.', 'type' => 'string'],
            ['key' => 'bank_account_name', 'value' => null, 'type' => 'string'],
            ['key' => 'bank_name', 'value' => null, 'type' => 'string'],
            ['key' => 'iban', 'value' => null, 'type' => 'string'],
            ['key' => 'swift_code', 'value' => null, 'type' => 'string'],
            ['key' => 'bank_transfer_instructions', 'value' => 'Please include your invoice number in the transfer reference.', 'type' => 'string'],
            ['key' => 'stripe_enabled', 'value' => false, 'type' => 'boolean'],
            ['key' => 'tabby_enabled', 'value' => false, 'type' => 'boolean'],
            ['key' => 'bank_transfer_enabled', 'value' => true, 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            $model = Setting::query()->firstOrNew(['group' => 'finance', 'key' => $setting['key']]);
            $model->fill([
                'type' => $setting['type'],
                'is_public' => in_array($setting['key'], ['company_legal_name', 'company_trn', 'invoice_footer_note', 'bank_account_name', 'bank_name', 'iban', 'swift_code', 'bank_transfer_instructions', 'stripe_enabled', 'tabby_enabled', 'bank_transfer_enabled'], true),
                'is_encrypted' => false,
            ]);
            $model->value = $model->encodeValue($setting['value']);
            $model->save();
        }
    }
}
