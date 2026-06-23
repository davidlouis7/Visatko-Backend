<?php

namespace App\Support\Cache;

trait ClearsPublicApiCache
{
    protected static function bootClearsPublicApiCache(): void
    {
        static::saved(fn (): null => app(PublicApiCache::class)->flush());
        static::deleted(fn (): null => app(PublicApiCache::class)->flush());
        if (method_exists(static::class, 'restored')) {
            static::restored(fn (): null => app(PublicApiCache::class)->flush());
        }
    }
}
