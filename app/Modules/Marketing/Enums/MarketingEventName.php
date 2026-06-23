<?php

namespace App\Modules\Marketing\Enums;

enum MarketingEventName: string
{
    case Lead = 'Lead';
    case CompleteRegistration = 'CompleteRegistration';
    case InitiateCheckout = 'InitiateCheckout';
    case Purchase = 'Purchase';
    case Contact = 'Contact';
    case SubmitApplication = 'SubmitApplication';
    case ConsultationSubmitted = 'ConsultationSubmitted';
    case InvoiceIssued = 'InvoiceIssued';
    case PaymentCompleted = 'PaymentCompleted';
    case RefundRequested = 'RefundRequested';
}
