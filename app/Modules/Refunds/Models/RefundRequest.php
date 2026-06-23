<?php

namespace App\Modules\Refunds\Models;

use App\Models\User;
use App\Modules\CreditNotes\Models\CreditNote;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Refunds\Enums\RefundRequestStatus;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundRequest extends Model
{
    use SoftDeletes;

    protected $fillable = ['refund_number', 'invoice_id', 'customer_id', 'visa_application_id', 'requested_by', 'approved_by', 'status', 'reason', 'amount', 'currency', 'provider', 'payment_transaction_id', 'credit_note_id', 'requested_at', 'reviewed_at', 'processed_at', 'internal_notes'];

    protected function casts(): array
    {
        return ['status' => RefundRequestStatus::class, 'amount' => 'decimal:2', 'requested_at' => 'datetime', 'reviewed_at' => 'datetime', 'processed_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'refund_number';
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function visaApplication(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class);
    }
}
