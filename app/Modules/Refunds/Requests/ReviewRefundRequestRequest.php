<?php

namespace App\Modules\Refunds\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRefundRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = match ($this->route()->getActionMethod()) {
            'approve' => 'refunds.approve',
            'reject' => 'refunds.reject',
            'process' => 'refunds.process',
            default => 'refunds.view',
        };

        return $this->user()?->can($permission) ?? false;
    }

    public function rules(): array
    {
        return ['internal_notes' => ['nullable', 'string']];
    }
}
