<?php

namespace App\Modules\Refunds\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'refund_number' => $this->refund_number,
            'invoice_id' => $this->invoice_id,
            'customer_id' => $this->customer_id,
            'visa_application_id' => $this->visa_application_id,
            'status' => $this->status->value ?? $this->status,
            'reason' => $this->reason,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'provider' => $this->provider,
            'payment_transaction_id' => $this->payment_transaction_id,
            'credit_note_id' => $this->credit_note_id,
            'requested_at' => $this->requested_at?->toISOString(),
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'processed_at' => $this->processed_at?->toISOString(),
            'internal_notes' => $this->internal_notes,
        ];
    }
}
