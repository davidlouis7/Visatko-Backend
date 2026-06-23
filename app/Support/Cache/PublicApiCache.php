<?php

namespace App\Support\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

class PublicApiCache
{
    public const TAG = 'public-api';

    public function remember(string $key, Closure $callback, ?int $seconds = null): mixed
    {
        $seconds ??= (int) env('PUBLIC_RESPONSE_CACHE_SECONDS', 300);

        if ($this->supportsTags()) {
            return Cache::tags([self::TAG])->remember($key, $seconds, $callback);
        }

        return Cache::remember(self::TAG.':'.$key, $seconds, $callback);
    }

    public function flush(): void
    {
        if ($this->supportsTags()) {
            Cache::tags([self::TAG])->flush();

            return;
        }

        Cache::flush();
    }

    private function supportsTags(): bool
    {
        return in_array(config('cache.default'), ['redis', 'memcached'], true);
    }
}
