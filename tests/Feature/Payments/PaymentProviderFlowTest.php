<?php

namespace Tests\Feature\Payments;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use App\Modules\Invoices\Actions\IssueInvoiceAction;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Models\PaymentTransaction;
use Database\Seeders\FinanceSettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentProviderFlowTest extends TestCase
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

    public function test_stripe_checkout_and_idempotent_success_webhook_update_invoice(): void
    {
        $invoice = $this->issuedInvoice();

        $checkout = $this->postJson("/api/v1/public/invoices/{$invoice->invoice_number}/pay/stripe")
            ->assertOk()
            ->assertJsonPath('data.transaction.status', 'pending');

        $sessionId = $checkout->json('data.transaction.provider_session_id');
        $payload = [
            'id' => 'evt_stripe_1',
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => $sessionId, 'client_reference_id' => $invoice->invoice_number, 'payment_intent' => 'pi_123']],
        ];

        $this->postJson('/api/v1/webhooks/stripe', $payload)->assertOk();
        $this->postJson('/api/v1/webhooks/stripe', $payload)->assertOk();

        $this->assertSame(1, PaymentTransaction::query()->where('webhook_event_id', 'evt_stripe_1')->count());
        $this->assertSame('paid', $invoice->refresh()->payment_status->value);
        $this->assertSame(105.0, (float) $invoice->amount_paid);
    }

    public function test_tabby_payment_and_success_webhook_update_invoice(): void
    {
        $invoice = $this->issuedInvoice();
        $checkout = $this->postJson("/api/v1/public/invoices/{$invoice->invoice_number}/pay/tabby")->assertOk();

        $this->postJson('/api/v1/webhooks/tabby', [
            'id' => 'evt_tabby_1',
            'payment' => [
                'id' => $checkout->json('data.transaction.provider_session_id'),
                'status' => 'paid',
                'order' => ['reference_id' => $invoice->invoice_number],
            ],
        ])->assertOk();

        $this->assertSame('paid', $invoice->refresh()->payment_status->value);
    }

    public function test_bank_transfer_upload_approval_and_rejection(): void
    {
        Storage::fake('local');
        $invoice = $this->issuedInvoice();

        $pending = $this->postJson("/api/v1/public/invoices/{$invoice->invoice_number}/pay/bank-transfer", [
            'amount' => 50,
            'receipt' => UploadedFile::fake()->image('receipt.jpg'),
        ])->assertCreated()->assertJsonPath('data.transaction.status', 'pending_review');

        $transactionNumber = $pending->json('data.transaction.transaction_number');
        $this->postJson("/api/v1/admin/bank-transfers/{$transactionNumber}/approve", ['notes' => 'Matched'])
            ->assertOk()
            ->assertJsonPath('data.status', 'paid');
        $this->assertSame('partially_paid', $invoice->refresh()->payment_status->value);

        $secondInvoice = $this->issuedInvoice();
        $rejected = $this->postJson("/api/v1/public/invoices/{$secondInvoice->invoice_number}/pay/bank-transfer", [
            'amount' => 25,
            'receipt' => UploadedFile::fake()->image('receipt2.jpg'),
        ])->assertCreated()->json('data.transaction.transaction_number');
        $this->postJson("/api/v1/admin/bank-transfers/{$rejected}/reject", ['notes' => 'Unreadable'])->assertOk()->assertJsonPath('data.status', 'failed');
    }

    protected function issuedInvoice(): Invoice
    {
        $invoiceNumber = $this->postJson('/api/v1/admin/invoices', [
            'customer_id' => $this->customer->id,
            'items' => [['description' => 'Visa', 'quantity' => 1, 'unit_price' => 100]],
        ])->assertCreated()->json('data.invoice_number');

        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->firstOrFail();
        app(IssueInvoiceAction::class)->execute($invoice, $this->admin);

        return $invoice->refresh();
    }
}
