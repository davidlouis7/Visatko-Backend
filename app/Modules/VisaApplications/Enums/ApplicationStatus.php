<?php

namespace App\Modules\VisaApplications\Enums;

enum ApplicationStatus: string
{
    case New = 'new';
    case PendingDocuments = 'pending_documents';
    case UnderReview = 'under_review';
    case PaymentPending = 'payment_pending';
    case Paid = 'paid';
    case Processing = 'processing';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Completed = 'completed';
}
