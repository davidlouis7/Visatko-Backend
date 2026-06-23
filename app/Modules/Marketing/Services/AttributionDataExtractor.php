<?php

namespace App\Modules\Marketing\Services;

use Illuminate\Http\Request;

class AttributionDataExtractor
{
    /** @return array<string, string|null> */
    public function fromRequest(Request $request): array
    {
        return collect(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'landing_page', 'referrer', 'gclid', 'fbclid', 'meta_event_id'])
            ->mapWithKeys(fn (string $key): array => [$key => $request->input($key)])
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->all();
    }
}
