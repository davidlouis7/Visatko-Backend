<?php

namespace App\Modules\Invoices\Models;

use App\Models\User;
use App\Modules\CRM\Models\Timeline;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Enums\InvoicePaymentStatus;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = ['invoice_number', 'customer_id', 'visa_application_id', 'created_by', 'status', 'payment_status', 'currency', 'subtotal', 'discount_total', 'taxable_amount', 'vat_rate', 'vat_amount', 'total', 'amount_paid', 'amount_due', 'issued_at', 'due_at', 'paid_at', 'notes', 'terms', 'meta'];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'payment_status' => InvoicePaymentStatus::class,
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
            'paid_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function visaApplication(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function timelines(): MorphMany
    {
        return $this->morphMany(Timeline::class, 'subject');
    }
}
