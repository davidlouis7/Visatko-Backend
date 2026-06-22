<?php

namespace App\Modules\Settings\Requests;

use App\Modules\Settings\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('setting')) === true;
    }

    public function rules(): array
    {
        /** @var Setting $setting */
        $setting = $this->route('setting');

        return [
            'group' => ['sometimes', 'required', 'string', 'max:100'],
            'key' => ['sometimes', 'required', 'string', 'max:150', 'regex:/^[a-z0-9_.-]+$/', Rule::unique('settings')->where('group', $this->input('group', $setting->group))->ignore($setting)],
            'value' => ['sometimes', 'required'],
            'type' => ['sometimes', 'required', Rule::in(['string', 'text', 'integer', 'decimal', 'boolean', 'json'])],
            'is_public' => ['sometimes', 'boolean'],
            'is_encrypted' => ['sometimes', 'boolean'],
        ];
    }
}
