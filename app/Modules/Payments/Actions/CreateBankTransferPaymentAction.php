<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Invoices\Enums\InvoicePaymentStatus;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Media\Services\FileUploadService;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\Payments\Events\BankTransferUploaded;
use App\Modules\Payments\Models\PaymentTransaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class CreateBankTransferPaymentAction
{
    public function __construct(private readonly FileUploadService $uploads) {}

    /** @param array<string, mixed> $data */
    public function execute(Invoice $invoice, UploadedFile $receipt, array $data): PaymentTransaction
    {
        if ($invoice->payment_status === InvoicePaymentStatus::Paid) {
            throw ValidationException::withMessages(['invoice' => 'Invoice is already paid.']);
        }

        $amount = round((float) $data['amount'], 2);
        if ($amount <= 0 || $amount > (float) $invoice->amount_due) {
            throw ValidationException::withMessages(['amount' => 'Transfer amount must not exceed amount due.']);
        }

        $media = $this->uploads->upload($receipt, 'bank_transfer_receipts', null, ['invoice_id' => $invoice->id]);
        $transaction = PaymentTransaction::query()->create([
            'transaction_number' => sprintf('PT-%s-%05d', now()->format('Y'), PaymentTransaction::withTrashed()->count() + 1),
            'invoice_id' => $invoice->id,
            'visa_application_id' => $invoice->visa_application_id,
            'customer_id' => $invoice->customer_id,
            'provider' => PaymentProvider::BankTransfer,
            'type' => PaymentTransactionType::Payment,
            'status' => PaymentTransactionStatus::PendingReview,
            'currency' => $invoice->currency,
            'amount' => $amount,
            'receipt_media_id' => $media->id,
            'notes' => $data['notes'] ?? null,
        ]);

        event(new BankTransferUploaded($transaction));

        return $transaction;
    }
}
