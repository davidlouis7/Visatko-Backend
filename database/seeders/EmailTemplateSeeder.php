<?php

namespace Database\Seeders;

use App\Modules\Emails\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'consultation_created_customer' => ['Consultation received', 'Hi {{customer_name}}, your consultation request has been received.'],
            'consultation_created_admin' => ['New consultation request', 'New consultation from {{customer_name}}. Phone: {{phone}}.'],
            'visa_application_created_customer' => ['Visa application received', 'Hi {{customer_name}}, your application {{application_number}} has been submitted.'],
            'visa_application_status_changed_customer' => ['Application status updated', 'Your application {{application_number}} status is now {{status}}.'],
            'document_rejected_customer' => ['Document needs attention', 'A document for application {{application_number}} needs attention: {{reason}}.'],
            'invoice_issued_customer' => ['Invoice {{invoice_number}} issued', 'Your invoice {{invoice_number}} for {{amount_due}} is ready: {{invoice_url}}.'],
            'invoice_paid_customer' => ['Invoice {{invoice_number}} paid', 'Thank you. Invoice {{invoice_number}} has been paid.'],
            'payment_failed_customer' => ['Payment failed', 'Payment for invoice {{invoice_number}} failed. Please try again.'],
            'bank_transfer_uploaded_admin' => ['Bank transfer uploaded', 'A bank transfer receipt was uploaded for invoice {{invoice_number}}.'],
            'refund_requested_admin' => ['Refund requested', 'Refund {{refund_number}} was requested for {{amount}}.'],
            'refund_approved_customer' => ['Refund approved', 'Your refund {{refund_number}} has been approved.'],
            'contact_message_admin' => ['New contact message', '{{customer_name}} sent a contact message: {{subject}}.'],
        ];

        foreach ($templates as $key => [$subject, $body]) {
            EmailTemplate::query()->updateOrCreate(['key' => $key], [
                'name' => str($key)->replace('_', ' ')->title()->toString(),
                'subject' => $subject,
                'body_html' => '<p>'.$body.'</p><p>{{frontend_url}}</p>',
                'body_text' => $body,
                'locale' => 'en',
                'variables' => [],
                'is_active' => true,
            ]);
        }
    }
}
