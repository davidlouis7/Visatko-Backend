<?php

namespace App\Modules\CreditNotes\Resources;

use App\Modules\Invoices\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'credit_note_number' => $this->credit_note_number,
            'invoice_id' => $this->invoice_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status->value ?? $this->status,
            'reason' => $this->reason,
            'subtotal' => (float) $this->subtotal,
            'vat_amount' => (float) $this->vat_amount,
            'total' => (float) $this->total,
            'issued_at' => $this->issued_at?->toISOString(),
            'invoice' => InvoiceResource::make($this->whenLoaded('invoice')),
            'items' => CreditNoteItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
