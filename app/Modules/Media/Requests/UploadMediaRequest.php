<?php

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadMediaRequest extends FormRequest
{
    public const COLLECTIONS = [
        'service_images', 'blog_images', 'team_images', 'partner_logos',
        'review_images', 'branch_images', 'passports', 'emirates_ids',
        'bank_transfer_proofs', 'application_documents',
    ];

    public function authorize(): bool
    {
        return $this->user()?->can('media.upload') === true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:'.(int) env('MAX_UPLOAD_KB', 5120), 'mimetypes:image/jpeg,image/png,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'collection' => ['required', Rule::in(self::COLLECTIONS)],
            'metadata' => ['sometimes', 'array'],
        ];
    }
}
