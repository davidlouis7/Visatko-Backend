<?php

namespace App\Modules\Team\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('team.create') ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'job_title' => ['required', 'string', 'max:255'], 'image_media_id' => ['nullable', 'exists:media,id'], 'bio' => ['nullable', 'string'], 'email' => ['nullable', 'email'], 'phone' => ['nullable', 'string', 'max:30'], 'social_links' => ['nullable', 'array'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer']];
    }
}
