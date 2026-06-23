<?php

namespace Tests\Feature\CreditNotes;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Actions\IssueInvoiceAction;
use App\Modules\Invoices\Models\Invoice;
use Database\Seeders\FinanceSettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreditNoteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_note_creation_issue_and_pdf(): void
    {
        $this->seed([RolePermissionSeeder::class, FinanceSettingSeeder::class]);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $customer = Customer::factory()->create();
        $invoiceNumber = $this->postJson('/api/v1/admin/invoices', [
            'customer_id' => $customer->id,
            'items' => [['description' => 'Visa', 'quantity' => 1, 'unit_price' => 100]],
        ])->assertCreated()->json('data.invoice_number');
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->first();
        app(IssueInvoiceAction::class)->execute($invoice, $admin);

        $creditNoteNumber = $this->postJson('/api/v1/admin/credit-notes', [
            'invoice_id' => $invoice->id,
            'reason' => 'Customer cancellation',
            'items' => [['invoice_item_id' => $invoice->items()->first()->id, 'description' => 'Refund', 'quantity' => 1, 'unit_price' => 50]],
        ])->assertCreated()->json('data.credit_note_number');

        $this->postJson("/api/v1/admin/credit-notes/{$creditNoteNumber}/issue")->assertOk()->assertJsonPath('data.status', 'issued');
        $this->get("/api/v1/admin/credit-notes/{$creditNoteNumber}/pdf")->assertOk();
    }
}
