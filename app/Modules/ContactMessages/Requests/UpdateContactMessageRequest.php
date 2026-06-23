<?php

namespace App\Modules\ContactMessages\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('contact_messages.update') ?? false;
    }

    public function rules(): array
    {
        return ['status' => ['sometimes', 'string', 'in:new,read,replied,closed'], 'assigned_to' => ['nullable', 'exists:users,id'], 'subject' => ['nullable', 'string', 'max:255']];
    }
}
