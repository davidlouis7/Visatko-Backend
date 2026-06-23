<?php

namespace App\Modules\Refunds\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefundRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('refunds.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'reason' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'provider' => ['nullable', 'string', 'max:40'],
            'payment_transaction_id' => ['nullable', 'exists:payment_transactions,id'],
            'internal_notes' => ['nullable', 'string'],
        ];
    }
}
