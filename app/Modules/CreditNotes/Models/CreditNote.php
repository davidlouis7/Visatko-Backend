<?php

namespace App\Modules\CreditNotes\Models;

use App\Models\User;
use App\Modules\CreditNotes\Enums\CreditNoteStatus;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNote extends Model
{
    use SoftDeletes;

    protected $fillable = ['credit_note_number', 'invoice_id', 'customer_id', 'created_by', 'status', 'reason', 'subtotal', 'vat_amount', 'total', 'issued_at', 'meta'];

    protected function casts(): array
    {
        return ['status' => CreditNoteStatus::class, 'subtotal' => 'decimal:2', 'vat_amount' => 'decimal:2', 'total' => 'decimal:2', 'issued_at' => 'datetime', 'meta' => 'array'];
    }

    public function getRouteKeyName(): string
    {
        return 'credit_note_number';
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CreditNoteItem::class);
    }
}
