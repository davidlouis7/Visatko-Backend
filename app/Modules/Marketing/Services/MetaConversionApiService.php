<?php

namespace App\Modules\Marketing\Services;

use App\Modules\Settings\Models\Setting;
use Illuminate\Support\Facades\Http;

class MetaConversionApiService
{
    public function enabled(): bool
    {
        return (bool) $this->setting('meta_capi_enabled', false) && (bool) $this->setting('meta_pixel_id') && (bool) $this->setting('meta_capi_token');
    }

    /** @param array<string, mixed> $payload */
    public function send(array $payload): array
    {
        $pixelId = $this->setting('meta_pixel_id');
        $token = $this->setting('meta_capi_token');
        $testCode = $this->setting('meta_capi_test_event_code');
        $body = ['data' => [$payload]];
        if ($testCode) {
            $body['test_event_code'] = $testCode;
        }

        return Http::post("https://graph.facebook.com/v20.0/{$pixelId}/events?access_token={$token}", $body)->throw()->json();
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('group', 'marketing')->where('key', $key)->first();

        return $setting ? $setting->resolvedValue() : $default;
    }
}
