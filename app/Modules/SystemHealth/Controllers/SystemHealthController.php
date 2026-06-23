<?php

namespace App\Modules\SystemHealth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SystemHealth\Services\SystemHealthService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class SystemHealthController extends Controller
{
    use ApiResponse;

    public function __invoke(SystemHealthService $health): JsonResponse
    {
        $result = $health->check();

        return $this->success($result, 'System health checked.', $result['status'] === 'unhealthy' ? 503 : 200);
    }
}
