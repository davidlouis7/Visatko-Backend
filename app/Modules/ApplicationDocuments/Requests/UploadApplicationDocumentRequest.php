<?php

namespace App\Modules\ApplicationDocuments\Requests;

use App\Modules\ApplicationDocuments\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadApplicationDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! $this->is('api/v1/admin/*') || $this->user()?->can('documents.upload') === true;
    }

    public function rules(): array
    {
        return ['file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx'], 'document_type' => ['required', Rule::enum(DocumentType::class)], 'title' => ['required', 'string', 'max:255']];
    }
}
