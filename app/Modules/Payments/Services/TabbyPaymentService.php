<?php

namespace App\Modules\Payments\Services;

use App\Modules\Invoices\Models\Invoice;
use Illuminate\Support\Facades\Http;

class TabbyPaymentService
{
    public function createPayment(Invoice $invoice): array
    {
        if (! config('services.tabby.secret')) {
            return [
                'id' => 'tabby_test_'.$invoice->invoice_number,
                'payment_url' => rtrim((string) config('app.url'), '/').'/mock-tabby-checkout/'.$invoice->invoice_number,
                'raw' => [],
            ];
        }

        $response = Http::withToken(config('services.tabby.secret'))->post('https://api.tabby.ai/api/v2/checkout', [
            'payment' => [
                'amount' => (string) $invoice->amount_due,
                'currency' => $invoice->currency,
                'buyer' => [
                    'name' => $invoice->customer->full_name,
                    'email' => $invoice->customer->email,
                    'phone' => $invoice->customer->phone,
                ],
                'order' => ['reference_id' => $invoice->invoice_number],
            ],
            'lang' => 'en',
            'merchant_code' => config('services.tabby.merchant_code'),
        ])->throw()->json();

        return [
            'id' => $response['id'] ?? $invoice->invoice_number,
            'payment_url' => $response['configuration']['available_products']['installments'][0]['web_url'] ?? null,
            'raw' => $response,
        ];
    }

    public function verifyWebhook(string $payload, ?string $signature): bool
    {
        $secret = config('services.tabby.webhook_secret');

        return ! $secret || hash_equals(hash_hmac('sha256', $payload, $secret), (string) $signature);
    }
}
