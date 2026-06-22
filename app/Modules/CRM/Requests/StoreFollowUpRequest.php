<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('follow_ups.create') === true;
    }

    public function rules(): array
    {
        return ['assigned_to' => ['required', 'integer', 'exists:users,id'], 'due_at' => ['required', 'date', 'after:now'], 'title' => ['required', 'string', 'max:255'], 'notes' => ['nullable', 'string', 'max:5000']];
    }
}
