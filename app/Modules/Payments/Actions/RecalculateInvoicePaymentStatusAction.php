<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Invoices\Enums\InvoicePaymentStatus;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\VisaApplications\Enums\PaymentStatus as ApplicationPaymentStatus;

class RecalculateInvoicePaymentStatusAction
{
    public function execute(Invoice $invoice): Invoice
    {
        $paid = (float) $invoice->transactions()
            ->where('type', PaymentTransactionType::Payment->value)
            ->where('status', PaymentTransactionStatus::Paid->value)
            ->sum('amount');
        $refunded = (float) $invoice->transactions()
            ->where('type', PaymentTransactionType::Refund->value)
            ->where('status', PaymentTransactionStatus::Refunded->value)
            ->sum('amount');
        $netPaid = max(0, round($paid - $refunded, 2));
        $amountDue = max(0, round((float) $invoice->total - $netPaid, 2));

        $paymentStatus = match (true) {
            $refunded >= (float) $invoice->total && $invoice->total > 0 => InvoicePaymentStatus::Refunded,
            $refunded > 0 => InvoicePaymentStatus::PartiallyRefunded,
            $netPaid >= (float) $invoice->total && $invoice->total > 0 => InvoicePaymentStatus::Paid,
            $netPaid > 0 => InvoicePaymentStatus::PartiallyPaid,
            $invoice->transactions()->where('status', PaymentTransactionStatus::Pending->value)->exists() => InvoicePaymentStatus::Pending,
            default => InvoicePaymentStatus::Unpaid,
        };

        $invoiceStatus = $invoice->status;
        if ($paymentStatus === InvoicePaymentStatus::Paid) {
            $invoiceStatus = InvoiceStatus::Paid;
        } elseif ($paymentStatus === InvoicePaymentStatus::PartiallyPaid) {
            $invoiceStatus = InvoiceStatus::PartiallyPaid;
        } elseif ($paymentStatus === InvoicePaymentStatus::Refunded) {
            $invoiceStatus = InvoiceStatus::Refunded;
        }

        $invoice->forceFill([
            'amount_paid' => $netPaid,
            'amount_due' => $amountDue,
            'payment_status' => $paymentStatus,
            'status' => $invoiceStatus,
            'paid_at' => $paymentStatus === InvoicePaymentStatus::Paid ? ($invoice->paid_at ?? now()) : $invoice->paid_at,
        ])->save();

        if ($invoice->visaApplication) {
            $applicationStatus = match ($paymentStatus) {
                InvoicePaymentStatus::Paid => ApplicationPaymentStatus::Paid,
                InvoicePaymentStatus::Pending, InvoicePaymentStatus::PartiallyPaid => ApplicationPaymentStatus::Pending,
                InvoicePaymentStatus::Refunded, InvoicePaymentStatus::PartiallyRefunded => ApplicationPaymentStatus::Refunded,
                InvoicePaymentStatus::Failed => ApplicationPaymentStatus::Failed,
                default => ApplicationPaymentStatus::Unpaid,
            };
            $invoice->visaApplication->forceFill(['payment_status' => $applicationStatus])->save();
        }

        return $invoice->refresh();
    }
}
