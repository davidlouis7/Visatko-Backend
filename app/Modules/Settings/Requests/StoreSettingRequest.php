<?php

namespace App\Modules\Settings\Requests;

use App\Modules\Settings\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Setting::class) === true;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'max:100'],
            'key' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9_.-]+$/', Rule::unique('settings')->where('group', $this->input('group'))],
            'value' => ['required'],
            'type' => ['required', Rule::in(['string', 'text', 'integer', 'decimal', 'boolean', 'json'])],
            'is_public' => ['sometimes', 'boolean', Rule::prohibitedIf($this->boolean('is_encrypted'))],
            'is_encrypted' => ['sometimes', 'boolean'],
        ];
    }
}
