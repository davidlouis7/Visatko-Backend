<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\CRM\Models\FollowUp;
use App\Modules\Reports\Services\ReportDateRangeResolver;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeePerformanceReportController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, ReportDateRangeResolver $range): JsonResponse
    {
        abort_unless($request->user()?->can('reports.employee_performance.view'), 403);
        [$from, $to] = $range->resolve($request);
        $users = User::query()->get()->map(fn (User $user): array => [
            'user_id' => $user->id,
            'name' => $user->name,
            'assigned_consultations_count' => $user->consultationsAssigned()->whereBetween('created_at', [$from, $to])->count(),
            'assigned_applications_count' => $user->applicationsAssigned()->whereBetween('created_at', [$from, $to])->count(),
            'completed_applications_count' => $user->applicationsAssigned()->where('status', 'completed')->whereBetween('updated_at', [$from, $to])->count(),
            'follow_ups_completed' => FollowUp::query()->where('assigned_to', $user->id)->where('status', 'completed')->whereBetween('completed_at', [$from, $to])->count(),
            'conversion_count' => $user->consultationsAssigned()->whereNotNull('converted_application_id')->whereBetween('updated_at', [$from, $to])->count(),
        ]);

        return $this->success($users);
    }
}
