<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Invoices\Enums\InvoicePaymentStatus;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Payments\Services\StripePaymentService;
use Illuminate\Validation\ValidationException;

class CreateStripeCheckoutAction
{
    public function __construct(private readonly StripePaymentService $stripe) {}

    public function execute(Invoice $invoice): array
    {
        if (! in_array($invoice->status, [InvoiceStatus::Issued, InvoiceStatus::Sent], true) || $invoice->payment_status === InvoicePaymentStatus::Paid) {
            throw ValidationException::withMessages(['invoice' => 'Invoice is not payable.']);
        }

        $session = $this->stripe->createCheckoutSession($invoice);
        $transaction = PaymentTransaction::query()->create([
            'transaction_number' => sprintf('PT-%s-%05d', now()->format('Y'), PaymentTransaction::withTrashed()->count() + 1),
            'invoice_id' => $invoice->id,
            'visa_application_id' => $invoice->visa_application_id,
            'customer_id' => $invoice->customer_id,
            'provider' => PaymentProvider::Stripe,
            'type' => PaymentTransactionType::Payment,
            'status' => PaymentTransactionStatus::Pending,
            'currency' => $invoice->currency,
            'amount' => $invoice->amount_due,
            'provider_session_id' => $session['id'],
        ]);

        return ['checkout_url' => $session['url'], 'transaction' => $transaction];
    }
}
