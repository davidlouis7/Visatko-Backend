<?php

namespace App\Modules\Emails\Listeners;

use App\Modules\ApplicationDocuments\Events\DocumentRejected;
use App\Modules\Consultations\Events\ConsultationCreated;
use App\Modules\ContactMessages\Events\ContactMessageCreated;
use App\Modules\Emails\Actions\SendTransactionalEmailAction;
use App\Modules\Invoices\Events\InvoiceIssued;
use App\Modules\Invoices\Events\InvoicePaid;
use App\Modules\Payments\Events\BankTransferUploaded;
use App\Modules\Payments\Events\PaymentFailed;
use App\Modules\Refunds\Events\RefundApproved;
use App\Modules\Refunds\Events\RefundRequested;
use App\Modules\Settings\Models\Setting;
use App\Modules\VisaApplications\Events\VisaApplicationCreated;
use App\Modules\VisaApplications\Events\VisaApplicationStatusChanged;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class SendDomainEmailListener
{
    public function __construct(private readonly SendTransactionalEmailAction $send) {}

    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof ConsultationCreated => $this->consultation($event),
                $event instanceof VisaApplicationCreated => $this->applicationCreated($event),
                $event instanceof VisaApplicationStatusChanged => $this->applicationStatus($event),
                $event instanceof DocumentRejected => $this->documentRejected($event),
                $event instanceof InvoiceIssued => $this->invoiceIssued($event),
                $event instanceof InvoicePaid => $this->invoicePaid($event),
                $event instanceof PaymentFailed => $this->paymentFailed($event),
                $event instanceof BankTransferUploaded => $this->admin('bank_transfer_uploaded_admin', ['invoice_number' => $event->transaction->invoice?->invoice_number], $event->transaction),
                $event instanceof RefundRequested => $this->admin('refund_requested_admin', ['refund_number' => $event->refundRequest->refund_number, 'amount' => $event->refundRequest->amount], $event->refundRequest),
                $event instanceof RefundApproved => $this->refundApproved($event),
                $event instanceof ContactMessageCreated => $this->admin('contact_message_admin', ['customer_name' => $event->contactMessage->full_name, 'subject' => $event->contactMessage->subject], $event->contactMessage),
                default => null,
            };
        } catch (Throwable) {
            report('Transactional email listener failed safely.');
        }
    }

    private function consultation(ConsultationCreated $event): void
    {
        $c = $event->consultation;
        $this->send->execute('consultation_created_customer', $c->email, $c->full_name, ['customer_name' => $c->full_name, 'phone' => $c->phone], $c);
        $this->admin('consultation_created_admin', ['customer_name' => $c->full_name, 'phone' => $c->phone], $c);
    }

    private function applicationCreated(VisaApplicationCreated $event): void
    {
        $a = $event->application;
        $this->send->execute('visa_application_created_customer', $a->email, $a->full_name, ['customer_name' => $a->full_name, 'application_number' => $a->application_number], $a);
    }

    private function applicationStatus(VisaApplicationStatusChanged $event): void
    {
        $a = $event->application;
        $this->send->execute('visa_application_status_changed_customer', $a->email, $a->full_name, ['application_number' => $a->application_number, 'status' => $a->status->value], $a);
    }

    private function documentRejected(DocumentRejected $event): void
    {
        $a = $event->document->application;
        $this->send->execute('document_rejected_customer', $a->email, $a->full_name, ['application_number' => $a->application_number, 'reason' => $event->document->rejection_reason], $event->document);
    }

    private function invoiceIssued(InvoiceIssued $event): void
    {
        $i = $event->invoice->loadMissing('customer');
        $this->send->execute('invoice_issued_customer', $i->customer->email, $i->customer->full_name, ['invoice_number' => $i->invoice_number, 'amount_due' => $i->amount_due, 'invoice_url' => rtrim((string) $this->setting('invoice_public_base_url', config('app.url').'/invoices'), '/').'/'.$i->invoice_number], $i);
    }

    private function invoicePaid(InvoicePaid $event): void
    {
        $i = $event->invoice->loadMissing('customer');
        $this->send->execute('invoice_paid_customer', $i->customer->email, $i->customer->full_name, ['invoice_number' => $i->invoice_number], $i);
    }

    private function paymentFailed(PaymentFailed $event): void
    {
        $i = $event->transaction->invoice?->loadMissing('customer');
        if ($i) {
            $this->send->execute('payment_failed_customer', $i->customer->email, $i->customer->full_name, ['invoice_number' => $i->invoice_number], $event->transaction);
        }
    }

    private function refundApproved(RefundApproved $event): void
    {
        $r = $event->refundRequest->loadMissing('customer');
        $this->send->execute('refund_approved_customer', $r->customer->email, $r->customer->full_name, ['refund_number' => $r->refund_number], $r);
    }

    private function admin(string $key, array $variables, object $related): void
    {
        $email = $this->setting('admin_notification_email');
        $this->send->execute($key, $email, 'Visatko Admin', $variables, $related instanceof Model ? $related : null);
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('group', 'email')->where('key', $key)->first();

        return $setting ? $setting->resolvedValue() : $default;
    }
}
