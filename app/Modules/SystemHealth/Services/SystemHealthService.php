<?php

namespace App\Modules\SystemHealth\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SystemHealthService
{
    public function check(): array
    {
        $checks = [
            'app' => ['status' => 'healthy'],
            'database' => $this->database(),
            'redis' => $this->redis(),
            'queue' => $this->queue(),
            'cache' => $this->cache(),
            'storage' => $this->storage(),
        ];

        $statuses = collect($checks)->pluck('status');
        $status = $statuses->contains('unhealthy') ? 'unhealthy' : ($statuses->contains('degraded') ? 'degraded' : 'healthy');

        return ['status' => $status, 'checks' => $checks];
    }

    private function database(): array
    {
        try {
            DB::select('select 1');

            return ['status' => 'healthy'];
        } catch (Throwable $exception) {
            return ['status' => 'unhealthy', 'message' => $exception->getMessage()];
        }
    }

    private function redis(): array
    {
        if (config('cache.default') !== 'redis' && config('queue.default') !== 'redis') {
            return ['status' => 'degraded', 'message' => 'Redis not configured as cache or queue driver.'];
        }

        try {
            Redis::connection()->ping();

            return ['status' => 'healthy'];
        } catch (Throwable $exception) {
            return ['status' => 'unhealthy', 'message' => $exception->getMessage()];
        }
    }

    private function queue(): array
    {
        return ['status' => config('queue.default') === 'sync' ? 'degraded' : 'healthy', 'message' => 'Queue driver: '.config('queue.default')];
    }

    private function cache(): array
    {
        try {
            Cache::put('health-check', now()->timestamp, 10);

            return Cache::has('health-check') ? ['status' => 'healthy'] : ['status' => 'unhealthy', 'message' => 'Cache write verification failed.'];
        } catch (Throwable $exception) {
            return ['status' => 'unhealthy', 'message' => $exception->getMessage()];
        }
    }

    private function storage(): array
    {
        try {
            $path = 'health-check.txt';
            Storage::disk(config('filesystems.default'))->put($path, 'ok');
            Storage::disk(config('filesystems.default'))->delete($path);

            return ['status' => 'healthy'];
        } catch (Throwable $exception) {
            return ['status' => 'unhealthy', 'message' => $exception->getMessage()];
        }
    }
}
