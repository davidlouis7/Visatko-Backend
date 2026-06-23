<?php

namespace App\Modules\Payments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankTransferUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'receipt' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf', 'max:'.(int) env('MAX_UPLOAD_KB', 5120)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
