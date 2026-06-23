<?php

namespace App\Modules\Payments\Actions;

use App\Models\User;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Events\BankTransferApproved;
use App\Modules\Payments\Events\BankTransferRejected;
use App\Modules\Payments\Models\PaymentTransaction;
use Illuminate\Validation\ValidationException;

class ReviewBankTransferPaymentAction
{
    public function __construct(private readonly RecalculateInvoicePaymentStatusAction $recalculate) {}

    public function approve(PaymentTransaction $transaction, ?User $user, ?string $notes = null): PaymentTransaction
    {
        $this->ensureBankTransfer($transaction);
        $transaction->forceFill(['status' => PaymentTransactionStatus::Paid, 'reviewed_by' => $user?->id, 'reviewed_at' => now(), 'paid_at' => now(), 'notes' => $notes ?? $transaction->notes])->save();
        if ($transaction->invoice) {
            $this->recalculate->execute($transaction->invoice);
        }
        event(new BankTransferApproved($transaction));

        return $transaction->refresh();
    }

    public function reject(PaymentTransaction $transaction, ?User $user, ?string $notes = null): PaymentTransaction
    {
        $this->ensureBankTransfer($transaction);
        $transaction->forceFill(['status' => PaymentTransactionStatus::Failed, 'reviewed_by' => $user?->id, 'reviewed_at' => now(), 'failed_at' => now(), 'notes' => $notes ?? $transaction->notes])->save();
        event(new BankTransferRejected($transaction));

        return $transaction->refresh();
    }

    private function ensureBankTransfer(PaymentTransaction $transaction): void
    {
        if ($transaction->provider !== PaymentProvider::BankTransfer || $transaction->status !== PaymentTransactionStatus::PendingReview) {
            throw ValidationException::withMessages(['transaction' => 'Only pending bank transfers can be reviewed.']);
        }
    }
}
