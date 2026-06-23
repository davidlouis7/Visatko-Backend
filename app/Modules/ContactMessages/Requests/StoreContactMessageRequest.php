<?php

namespace App\Modules\ContactMessages\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['full_name' => ['required', 'string', 'max:150'], 'email' => ['nullable', 'email:rfc', 'max:254'], 'phone' => ['nullable', 'string', 'max:30'], 'subject' => ['nullable', 'string', 'max:255'], 'message' => ['required', 'string', 'max:5000'], 'source' => ['nullable', 'string', 'max:100'], 'utm_source' => ['nullable', 'string', 'max:255'], 'utm_medium' => ['nullable', 'string', 'max:255'], 'utm_campaign' => ['nullable', 'string', 'max:255'], 'utm_content' => ['nullable', 'string', 'max:255'], 'utm_term' => ['nullable', 'string', 'max:255'], 'landing_page' => ['nullable', 'string', 'max:1000'], 'referrer' => ['nullable', 'string', 'max:1000'], 'gclid' => ['nullable', 'string', 'max:255'], 'fbclid' => ['nullable', 'string', 'max:255'], 'meta_event_id' => ['nullable', 'string', 'max:255']];
    }
}
