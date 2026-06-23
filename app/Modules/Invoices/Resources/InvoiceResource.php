<?php

namespace App\Modules\Invoices\Resources;

use App\Modules\Customers\Resources\CustomerResource;
use App\Modules\Payments\Resources\PaymentTransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'visa_application_id' => $this->visa_application_id,
            'status' => $this->status->value ?? $this->status,
            'payment_status' => $this->payment_status->value ?? $this->payment_status,
            'currency' => $this->currency,
            'subtotal' => (float) $this->subtotal,
            'discount_total' => (float) $this->discount_total,
            'taxable_amount' => (float) $this->taxable_amount,
            'vat_rate' => (float) $this->vat_rate,
            'vat_amount' => (float) $this->vat_amount,
            'total' => (float) $this->total,
            'amount_paid' => (float) $this->amount_paid,
            'amount_due' => (float) $this->amount_due,
            'issued_at' => $this->issued_at?->toISOString(),
            'due_at' => $this->due_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),
            'notes' => $this->when(! $request->routeIs('api.v1.public.*'), $this->notes),
            'terms' => $this->terms,
            'meta' => $this->when(! $request->routeIs('api.v1.public.*'), $this->meta),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'transactions' => PaymentTransactionResource::collection($this->whenLoaded('transactions')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
