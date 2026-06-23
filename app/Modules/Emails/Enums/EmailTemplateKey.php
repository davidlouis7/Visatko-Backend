<?php

namespace App\Modules\Emails\Enums;

enum EmailTemplateKey: string
{
    case ConsultationCreatedCustomer = 'consultation_created_customer';
    case ConsultationCreatedAdmin = 'consultation_created_admin';
    case VisaApplicationCreatedCustomer = 'visa_application_created_customer';
    case VisaApplicationStatusChangedCustomer = 'visa_application_status_changed_customer';
    case DocumentRejectedCustomer = 'document_rejected_customer';
    case InvoiceIssuedCustomer = 'invoice_issued_customer';
    case InvoicePaidCustomer = 'invoice_paid_customer';
    case PaymentFailedCustomer = 'payment_failed_customer';
    case BankTransferUploadedAdmin = 'bank_transfer_uploaded_admin';
    case RefundRequestedAdmin = 'refund_requested_admin';
    case RefundApprovedCustomer = 'refund_approved_customer';
    case ContactMessageAdmin = 'contact_message_admin';
}
