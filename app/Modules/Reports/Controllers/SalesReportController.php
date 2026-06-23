<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Refunds\Enums\RefundRequestStatus;
use App\Modules\Refunds\Models\RefundRequest;
use App\Modules\Reports\Services\ReportDateRangeResolver;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, ReportDateRangeResolver $range): JsonResponse
    {
        abort_unless($request->user()?->can('reports.sales.view'), 403);
        [$from, $to] = $range->resolve($request);
        $payments = PaymentTransaction::query()->where('type', PaymentTransactionType::Payment->value)->where('status', PaymentTransactionStatus::Paid->value)->whereBetween('paid_at', [$from, $to]);
        $revenue = (float) (clone $payments)->sum('amount');
        $refunds = (float) RefundRequest::query()->where('status', RefundRequestStatus::Processed->value)->whereBetween('processed_at', [$from, $to])->sum('amount');

        return $this->success([
            'revenue_by_day' => (clone $payments)->selectRaw('date(paid_at) as day, sum(amount) as total')->groupBy('day')->pluck('total', 'day'),
            'revenue_by_payment_provider' => (clone $payments)->selectRaw('provider, sum(amount) as total')->groupBy('provider')->pluck('total', 'provider'),
            'revenue_by_visa_service' => [],
            'paid_invoices_count' => (clone $payments)->distinct('invoice_id')->count('invoice_id'),
            'refunds_total' => $refunds,
            'net_revenue' => round($revenue - $refunds, 2),
        ]);
    }
}
