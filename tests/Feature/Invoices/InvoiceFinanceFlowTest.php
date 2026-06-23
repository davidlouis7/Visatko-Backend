<?php

namespace Tests\Feature\Invoices;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Models\Invoice;
use Database\Seeders\FinanceSettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceFinanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, FinanceSettingSeeder::class]);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
        Sanctum::actingAs($this->admin);
        $this->customer = Customer::factory()->create();
    }

    public function test_admin_can_create_issue_download_and_mark_invoice_paid_with_server_totals(): void
    {
        $invoiceNumber = $this->createInvoice();
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->first();

        $this->assertSame(199.50, (float) $invoice->total);
        $this->assertSame(0.0, (float) $invoice->amount_paid);

        $this->postJson("/api/v1/admin/invoices/{$invoiceNumber}/issue")->assertOk()->assertJsonPath('data.status', 'issued');
        $this->patchJson("/api/v1/admin/invoices/{$invoiceNumber}", [
            'items' => [['description' => 'Tamper', 'quantity' => 1, 'unit_price' => 1]],
        ])->assertUnprocessable();
        $this->get("/api/v1/admin/invoices/{$invoiceNumber}/pdf")->assertOk();
        $this->postJson("/api/v1/admin/invoices/{$invoiceNumber}/mark-paid", ['amount' => 199.50, 'reference' => 'cash'])
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.amount_due', 0);

        $this->assertDatabaseHas('payment_transactions', ['invoice_id' => $invoice->id, 'provider' => 'manual', 'status' => 'paid']);
        $this->getJson("/api/v1/public/invoices/{$invoiceNumber}")->assertOk()->assertJsonMissing(['internal_notes']);
    }

    public function test_permission_protection_blocks_unprivileged_users(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/admin/invoices', [])->assertForbidden();
    }

    protected function createInvoice(): string
    {
        return $this->postJson('/api/v1/admin/invoices', [
            'customer_id' => $this->customer->id,
            'currency' => 'AED',
            'items' => [
                ['description' => 'Visa service', 'quantity' => 2, 'unit_price' => 100, 'discount_amount' => 10],
            ],
            'subtotal' => 1,
            'total' => 1,
        ])->assertCreated()->json('data.invoice_number');
    }
}
