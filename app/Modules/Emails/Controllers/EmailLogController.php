<?php

namespace App\Modules\Emails\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Emails\Models\EmailLog;
use App\Modules\Emails\Resources\EmailLogResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmailLogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', EmailLog::class);
        $logs = EmailLog::query()->latest()->paginate((int) $request->integer('per_page', 25));

        return $this->paginated($logs, EmailLogResource::collection($logs));
    }

    public function show(EmailLog $emailLog): JsonResponse
    {
        Gate::authorize('view', $emailLog);

        return $this->success(EmailLogResource::make($emailLog));
    }
}
