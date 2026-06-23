<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Refunds\Enums\RefundRequestStatus;
use App\Modules\Refunds\Models\RefundRequest;
use App\Modules\Reports\Services\ReportDateRangeResolver;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardReportController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, ReportDateRangeResolver $range): JsonResponse
    {
        abort_unless($request->user()?->can('reports.dashboard.view'), 403);
        [$from, $to] = $range->resolve($request);

        $data = Cache::remember('reports.dashboard:'.$from->toDateString().':'.$to->toDateString(), (int) env('REPORT_CACHE_SECONDS', 120), fn (): array => [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_customers' => Customer::query()->count(),
            'total_consultations' => Consultation::query()->count(),
            'total_visa_applications' => VisaApplication::query()->count(),
            'total_invoices' => Invoice::query()->count(),
            'total_revenue' => (float) PaymentTransaction::query()->where('type', PaymentTransactionType::Payment->value)->where('status', PaymentTransactionStatus::Paid->value)->whereBetween('paid_at', [$from, $to])->sum('amount'),
            'amount_due' => (float) Invoice::query()->sum('amount_due'),
            'pending_bank_transfers' => PaymentTransaction::query()->where('provider', 'bank_transfer')->where('status', 'pending_review')->count(),
            'pending_refunds' => RefundRequest::query()->whereIn('status', [RefundRequestStatus::Requested->value, RefundRequestStatus::Approved->value])->count(),
            'applications_by_status' => VisaApplication::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'consultations_by_status' => Consultation::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'invoices_by_status' => Invoice::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'payments_by_provider' => PaymentTransaction::query()->where('status', PaymentTransactionStatus::Paid->value)->selectRaw('provider, sum(amount) as total')->groupBy('provider')->pluck('total', 'provider'),
            'recent_consultations' => Consultation::query()->latest()->limit(5)->get(['id', 'full_name', 'phone', 'status', 'created_at']),
            'recent_applications' => VisaApplication::query()->latest()->limit(5)->get(['id', 'application_number', 'full_name', 'status', 'created_at']),
            'recent_payments' => PaymentTransaction::query()->latest()->limit(5)->get(['id', 'transaction_number', 'provider', 'status', 'amount', 'created_at']),
        ]);

        return $this->success($data);
    }
}
