<?php

namespace App\Modules\Marketing\Listeners;

use App\Modules\Consultations\Events\ConsultationCreated;
use App\Modules\ContactMessages\Events\ContactMessageCreated;
use App\Modules\Invoices\Events\InvoiceIssued;
use App\Modules\Marketing\Actions\SendMetaConversionEventAction;
use App\Modules\Marketing\Enums\MarketingEventName;
use App\Modules\Payments\Events\PaymentSucceeded;
use App\Modules\Refunds\Events\RefundRequested;
use App\Modules\VisaApplications\Events\VisaApplicationCreated;
use Throwable;

class SendDomainMarketingEventListener
{
    public function __construct(private readonly SendMetaConversionEventAction $send) {}

    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof ConsultationCreated => $this->consultation($event),
                $event instanceof VisaApplicationCreated => $this->application($event),
                $event instanceof InvoiceIssued => $this->invoice($event),
                $event instanceof PaymentSucceeded => $this->payment($event),
                $event instanceof ContactMessageCreated => $this->contact($event),
                $event instanceof RefundRequested => $this->refund($event),
                default => null,
            };
        } catch (Throwable) {
            report('Marketing event listener failed safely.');
        }
    }

    private function consultation(ConsultationCreated $event): void
    {
        $c = $event->consultation->loadMissing('customer');
        $payload = $c->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'meta_event_id']);
        $this->send->execute(MarketingEventName::Lead, $c, $c->customer, $payload);
        $this->send->execute(MarketingEventName::ConsultationSubmitted, $c, $c->customer, [...$payload, 'event_id' => ($c->meta_event_id ?: 'consultation-'.$c->id).'-submitted']);
    }

    private function application(VisaApplicationCreated $event): void
    {
        $a = $event->application->loadMissing('customer');
        $this->send->execute(MarketingEventName::SubmitApplication, $a, $a->customer, $a->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'meta_event_id']));
    }

    private function invoice(InvoiceIssued $event): void
    {
        $i = $event->invoice->loadMissing('customer');
        $this->send->execute(MarketingEventName::InitiateCheckout, $i, $i->customer, ['invoice_number' => $i->invoice_number, 'value' => $i->total, 'currency' => $i->currency]);
        $this->send->execute(MarketingEventName::InvoiceIssued, $i, $i->customer, ['event_id' => 'invoice-issued-'.$i->id, 'invoice_number' => $i->invoice_number, 'value' => $i->total, 'currency' => $i->currency]);
    }

    private function payment(PaymentSucceeded $event): void
    {
        $transaction = $event->transaction->loadMissing(['invoice.customer']);
        $customer = $transaction->invoice?->customer ?: $transaction->customer;
        $this->send->execute(MarketingEventName::Purchase, $transaction, $customer, ['transaction_number' => $transaction->transaction_number, 'value' => $transaction->amount, 'currency' => $transaction->currency]);
        $this->send->execute(MarketingEventName::PaymentCompleted, $transaction, $customer, ['event_id' => 'payment-completed-'.$transaction->id, 'transaction_number' => $transaction->transaction_number, 'value' => $transaction->amount, 'currency' => $transaction->currency]);
    }

    private function contact(ContactMessageCreated $event): void
    {
        $m = $event->contactMessage;
        $this->send->execute(MarketingEventName::Contact, $m, null, $m->only(['email', 'phone', 'utm_source', 'utm_medium', 'utm_campaign', 'meta_event_id']));
    }

    private function refund(RefundRequested $event): void
    {
        $r = $event->refundRequest->loadMissing('customer');
        $this->send->execute(MarketingEventName::RefundRequested, $r, $r->customer, ['refund_number' => $r->refund_number, 'value' => $r->amount, 'currency' => $r->currency]);
    }
}
