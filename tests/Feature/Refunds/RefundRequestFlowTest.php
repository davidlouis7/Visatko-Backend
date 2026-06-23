<?php

namespace Tests\Feature\Refunds;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Actions\IssueInvoiceAction;
use App\Modules\Invoices\Actions\MarkInvoiceAsPaidAction;
use App\Modules\Invoices\Models\Invoice;
use Database\Seeders\FinanceSettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RefundRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_request_approval_rejection_and_processing(): void
    {
        $this->seed([RolePermissionSeeder::class, FinanceSettingSeeder::class]);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $invoice = $this->paidInvoice($admin);

        $refundNumber = $this->postJson('/api/v1/admin/refund-requests', [
            'invoice_id' => $invoice->id,
            'reason' => 'Duplicate payment',
            'amount' => 25,
        ])->assertCreated()->json('data.refund_number');

        $this->postJson("/api/v1/admin/refund-requests/{$refundNumber}/approve", ['internal_notes' => 'Approved'])->assertOk()->assertJsonPath('data.status', 'approved');
        $this->postJson("/api/v1/admin/refund-requests/{$refundNumber}/process", ['internal_notes' => 'Processed manually'])
            ->assertOk()
            ->assertJsonPath('data.status', 'processed')
            ->assertJsonPath('data.credit_note_id', 1);
        $this->assertSame('partially_refunded', $invoice->refresh()->payment_status->value);

        $second = $this->postJson('/api/v1/admin/refund-requests', [
            'invoice_id' => $invoice->id,
            'reason' => 'Not eligible',
            'amount' => 10,
        ])->assertCreated()->json('data.refund_number');
        $this->postJson("/api/v1/admin/refund-requests/{$second}/reject", ['internal_notes' => 'Rejected'])->assertOk()->assertJsonPath('data.status', 'rejected');
    }

    protected function paidInvoice(User $admin): Invoice
    {
        $customer = Customer::factory()->create();
        $invoiceNumber = $this->postJson('/api/v1/admin/invoices', [
            'customer_id' => $customer->id,
            'items' => [['description' => 'Visa', 'quantity' => 1, 'unit_price' => 100]],
        ])->assertCreated()->json('data.invoice_number');
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->first();
        app(IssueInvoiceAction::class)->execute($invoice, $admin);
        app(MarkInvoiceAsPaidAction::class)->execute($invoice, ['amount' => 105], $admin);

        return $invoice->refresh();
    }
}
