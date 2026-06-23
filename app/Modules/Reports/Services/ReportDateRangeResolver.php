<?php

namespace App\Modules\Reports\Services;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ReportDateRangeResolver
{
    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    public function resolve(Request $request): array
    {
        $period = $request->query('period', 'this_month');

        return match ($period) {
            'today' => [CarbonImmutable::today(), CarbonImmutable::today()->endOfDay()],
            'this_week' => [CarbonImmutable::now()->startOfWeek(), CarbonImmutable::now()->endOfWeek()],
            'this_year' => [CarbonImmutable::now()->startOfYear(), CarbonImmutable::now()->endOfYear()],
            'custom' => [CarbonImmutable::parse($request->query('from', now()->startOfMonth())), CarbonImmutable::parse($request->query('to', now()))->endOfDay()],
            default => [CarbonImmutable::now()->startOfMonth(), CarbonImmutable::now()->endOfMonth()],
        };
    }
}
