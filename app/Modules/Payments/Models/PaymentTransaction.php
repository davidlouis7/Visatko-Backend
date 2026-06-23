<?php

namespace App\Modules\Payments\Models;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Media\Models\Media;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = ['transaction_number', 'invoice_id', 'visa_application_id', 'customer_id', 'provider', 'type', 'status', 'currency', 'amount', 'provider_reference', 'provider_session_id', 'provider_payment_intent_id', 'webhook_event_id', 'receipt_media_id', 'raw_payload', 'paid_at', 'failed_at', 'reviewed_by', 'reviewed_at', 'notes'];

    protected function casts(): array
    {
        return ['provider' => PaymentProvider::class, 'type' => PaymentTransactionType::class, 'status' => PaymentTransactionStatus::class, 'amount' => 'decimal:2', 'raw_payload' => 'array', 'paid_at' => 'datetime', 'failed_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'transaction_number';
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'receipt_media_id');
    }
}
