<?php

namespace App\Modules\Payments\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_number' => $this->transaction_number,
            'invoice_id' => $this->invoice_id,
            'customer_id' => $this->customer_id,
            'provider' => $this->provider->value ?? $this->provider,
            'type' => $this->type->value ?? $this->type,
            'status' => $this->status->value ?? $this->status,
            'currency' => $this->currency,
            'amount' => (float) $this->amount,
            'provider_reference' => $this->provider_reference,
            'provider_session_id' => $this->provider_session_id,
            'paid_at' => $this->paid_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
