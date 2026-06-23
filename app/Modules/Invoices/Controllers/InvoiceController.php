<?php

namespace App\Modules\Invoices\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Resources\TimelineResource;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Actions\CalculateInvoiceTotalsAction;
use App\Modules\Invoices\Actions\CreateInvoiceAction;
use App\Modules\Invoices\Actions\GenerateInvoicePdfAction;
use App\Modules\Invoices\Actions\IssueInvoiceAction;
use App\Modules\Invoices\Actions\MarkInvoiceAsPaidAction;
use App\Modules\Invoices\Actions\MarkInvoiceAsSentAction;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Invoices\Requests\MarkInvoicePaidRequest;
use App\Modules\Invoices\Requests\StoreInvoiceRequest;
use App\Modules\Invoices\Requests\UpdateInvoiceRequest;
use App\Modules\Invoices\Resources\InvoiceResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Invoice::class);
        $paginator = Invoice::query()->with(['customer'])->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($paginator, InvoiceResource::collection($paginator));
    }

    public function store(StoreInvoiceRequest $request, CreateInvoiceAction $action): JsonResponse
    {
        $invoice = $action->execute($request->validated(), $request->user());

        return $this->success(InvoiceResource::make($invoice), 'Invoice created successfully.', 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        Gate::authorize('view', $invoice);

        return $this->success(InvoiceResource::make($invoice->load(['customer', 'items', 'transactions'])));
    }

    public function publicShow(string $invoiceNumber, FinanceSettings $settings): JsonResponse
    {
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->with(['customer', 'items'])->firstOrFail();

        return $this->success([
            'invoice' => InvoiceResource::make($invoice),
            'payment_config' => $settings->publicPaymentConfig(),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice, CalculateInvoiceTotalsAction $totals): JsonResponse
    {
        if ($invoice->status !== InvoiceStatus::Draft && $request->has('items')) {
            return $this->error('Issued invoices cannot have financial items edited.', 422);
        }

        $invoice->fill($request->safe()->except('items'));

        if ($request->has('items')) {
            $calculated = $totals->execute($request->input('items'));
            $invoice->fill([
                'subtotal' => $calculated['subtotal'],
                'discount_total' => $calculated['discount_total'],
                'taxable_amount' => $calculated['taxable_amount'],
                'vat_rate' => $calculated['vat_rate'],
                'vat_amount' => $calculated['vat_amount'],
                'total' => $calculated['total'],
                'amount_due' => max(0, $calculated['total'] - (float) $invoice->amount_paid),
            ]);
            $invoice->items()->delete();
            $invoice->save();
            foreach ($calculated['items'] as $line) {
                $invoice->items()->create($line);
            }
        } else {
            $invoice->save();
        }

        activity('finance')->causedBy($request->user())->performedOn($invoice)->log('Invoice updated');

        return $this->success(InvoiceResource::make($invoice->refresh()->load(['customer', 'items'])), 'Invoice updated successfully.');
    }

    public function issue(Invoice $invoice, IssueInvoiceAction $action): JsonResponse
    {
        Gate::authorize('issue', $invoice);

        return $this->success(InvoiceResource::make($action->execute($invoice, request()->user())), 'Invoice issued successfully.');
    }

    public function markSent(Invoice $invoice, MarkInvoiceAsSentAction $action): JsonResponse
    {
        Gate::authorize('send', $invoice);

        return $this->success(InvoiceResource::make($action->execute($invoice, request()->user())), 'Invoice marked as sent.');
    }

    public function markPaid(MarkInvoicePaidRequest $request, Invoice $invoice, MarkInvoiceAsPaidAction $action): JsonResponse
    {
        return $this->success(InvoiceResource::make($action->execute($invoice, $request->validated(), $request->user())), 'Payment recorded successfully.');
    }

    public function timeline(Invoice $invoice): JsonResponse
    {
        Gate::authorize('view', $invoice);

        return $this->success(TimelineResource::collection($invoice->timelines()->with('user')->latest('created_at')->get()));
    }

    public function pdf(Invoice $invoice, GenerateInvoicePdfAction $action): Response
    {
        Gate::authorize('download', $invoice);

        return $action->execute($invoice)->stream($invoice->invoice_number.'.pdf');
    }

    public function publicPdf(string $invoiceNumber, GenerateInvoicePdfAction $action): Response
    {
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->firstOrFail();

        return $action->execute($invoice)->stream($invoice->invoice_number.'.pdf');
    }
}
