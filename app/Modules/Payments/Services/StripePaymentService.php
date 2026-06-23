<?php

namespace App\Modules\Payments\Services;

use App\Modules\Invoices\Models\Invoice;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class StripePaymentService
{
    public function createCheckoutSession(Invoice $invoice): array
    {
        if (! config('services.stripe.secret')) {
            return [
                'id' => 'cs_test_'.$invoice->invoice_number,
                'url' => rtrim((string) config('app.url'), '/').'/mock-stripe-checkout/'.$invoice->invoice_number,
            ];
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::create([
            'mode' => 'payment',
            'client_reference_id' => $invoice->invoice_number,
            'success_url' => rtrim((string) config('services.frontend_url', config('app.url')), '/').'/invoices/'.$invoice->invoice_number.'?payment=success',
            'cancel_url' => rtrim((string) config('services.frontend_url', config('app.url')), '/').'/invoices/'.$invoice->invoice_number.'?payment=cancelled',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($invoice->currency),
                    'unit_amount' => (int) round(((float) $invoice->amount_due) * 100),
                    'product_data' => ['name' => 'Invoice '.$invoice->invoice_number],
                ],
            ]],
            'metadata' => ['invoice_number' => $invoice->invoice_number],
        ]);

        return ['id' => $session->id, 'url' => $session->url];
    }

    public function constructWebhookEvent(string $payload, ?string $signature): object
    {
        $secret = config('services.stripe.webhook_secret');

        if ($secret && $signature) {
            return Webhook::constructEvent($payload, $signature, $secret);
        }

        return json_decode($payload, false, flags: JSON_THROW_ON_ERROR);
    }
}
