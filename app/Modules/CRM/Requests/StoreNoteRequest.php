<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('notes.create') === true;
    }

    public function rules(): array
    {
        return ['note' => ['required', 'string', 'max:10000'], 'is_private' => ['sometimes', 'boolean']];
    }
}
