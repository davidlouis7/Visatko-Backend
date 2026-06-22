<?php

namespace App\Modules\ApplicationDocuments\Requests;

use App\Modules\ApplicationDocuments\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewApplicationDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('documents.review') === true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in([DocumentStatus::Accepted->value, DocumentStatus::Rejected->value, DocumentStatus::NeedsReupload->value])], 'rejection_reason' => ['nullable', 'string', 'max:5000', Rule::requiredIf(in_array($this->input('status'), ['rejected', 'needs_reupload'], true))]];
    }
}
