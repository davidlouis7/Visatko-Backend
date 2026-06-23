<?php

namespace App\Modules\Marketing\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Marketing\Models\MarketingEvent;
use App\Modules\Marketing\Resources\MarketingEventResource;
use App\Modules\Settings\Models\Setting;
use App\Support\Cache\PublicApiCache;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MarketingEventController extends Controller
{
    use ApiResponse;

    public function publicTrackingSettings(PublicApiCache $cache): JsonResponse
    {
        $settings = $cache->remember('tracking.settings', function () {
            $safe = ['meta_pixel_enabled', 'meta_pixel_id', 'google_analytics_enabled', 'google_analytics_id', 'google_tag_manager_enabled', 'google_tag_manager_id'];

            return Setting::query()->where('group', 'marketing')->whereIn('key', $safe)->get()->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->resolvedValue()])->all();
        });

        return $this->success($settings);
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MarketingEvent::class);
        $items = MarketingEvent::query()->latest()->paginate((int) $request->integer('per_page', 25));

        return $this->paginated($items, MarketingEventResource::collection($items));
    }

    public function show(MarketingEvent $marketingEvent): JsonResponse
    {
        Gate::authorize('view', $marketingEvent);

        return $this->success(MarketingEventResource::make($marketingEvent));
    }
}
