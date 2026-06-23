<?php

use App\Modules\ApplicationDocuments\Controllers\ApplicationDocumentController;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Blog\Controllers\BlogCategoryController;
use App\Modules\Blog\Controllers\BlogPostController;
use App\Modules\Blog\Controllers\BlogTagController;
use App\Modules\Branches\Controllers\BranchController;
use App\Modules\Consultations\Controllers\ConsultationController;
use App\Modules\ContactMessages\Controllers\ContactMessageController;
use App\Modules\Counters\Controllers\CounterController;
use App\Modules\Countries\Controllers\CountryController;
use App\Modules\CreditNotes\Controllers\CreditNoteController;
use App\Modules\CRM\Controllers\FollowUpController;
use App\Modules\Customers\Controllers\CustomerController;
use App\Modules\Emails\Controllers\EmailLogController;
use App\Modules\Emails\Controllers\EmailTemplateController;
use App\Modules\Invoices\Controllers\InvoiceController;
use App\Modules\Languages\Controllers\LanguageController;
use App\Modules\Marketing\Controllers\MarketingEventController;
use App\Modules\Media\Controllers\MediaController;
use App\Modules\Pages\Controllers\PageController;
use App\Modules\Partners\Controllers\PartnerController;
use App\Modules\Payments\Controllers\BankTransferPaymentController;
use App\Modules\Payments\Controllers\PaymentTransactionController;
use App\Modules\Payments\Controllers\StripeWebhookController;
use App\Modules\Payments\Controllers\TabbyWebhookController;
use App\Modules\Refunds\Controllers\RefundRequestController;
use App\Modules\Reports\Controllers\DashboardReportController;
use App\Modules\Reports\Controllers\EmployeePerformanceReportController;
use App\Modules\Reports\Controllers\SalesReportController;
use App\Modules\Reviews\Controllers\ReviewController;
use App\Modules\Settings\Controllers\SettingController;
use App\Modules\SystemHealth\Controllers\SystemHealthController;
use App\Modules\Team\Controllers\TeamMemberController;
use App\Modules\VisaApplications\Controllers\VisaApplicationController;
use App\Modules\VisaServices\Controllers\VisaServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('system/health', SystemHealthController::class)->middleware('throttle:public-read');

    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login')
        ->name('api.v1.auth.login');
    Route::get('languages', [LanguageController::class, 'publicIndex']);
    Route::get('settings/public', [SettingController::class, 'publicIndex']);
    Route::get('countries', [CountryController::class, 'publicIndex']);
    Route::get('countries/{slug}', [CountryController::class, 'publicShow']);
    Route::get('visa-services/featured', [VisaServiceController::class, 'featured']);
    Route::get('visa-services', [VisaServiceController::class, 'publicIndex']);
    Route::get('visa-services/{slug}', [VisaServiceController::class, 'publicShow']);
    Route::get('blog/posts', [BlogPostController::class, 'publicIndex']);
    Route::get('blog/posts/{slug}', [BlogPostController::class, 'publicShow']);
    Route::get('pages', [PageController::class, 'publicIndex']);
    Route::get('pages/{slug}', [PageController::class, 'publicShow']);
    Route::get('public/tracking/settings', [MarketingEventController::class, 'publicTrackingSettings']);
    Route::post('public/contact-messages', [ContactMessageController::class, 'store'])->middleware('throttle:public-submit');
    Route::get('public/reviews', [ReviewController::class, 'publicIndex']);
    Route::get('public/reviews/featured', [ReviewController::class, 'featured']);
    Route::get('public/counters', [CounterController::class, 'publicIndex']);
    Route::get('public/team-members', [TeamMemberController::class, 'publicIndex']);
    Route::get('public/partners', [PartnerController::class, 'publicIndex']);
    Route::get('public/branches', [BranchController::class, 'publicIndex']);

    Route::post('public/consultations', [ConsultationController::class, 'store'])
        ->middleware('throttle:public-submit')
        ->name('api.v1.consultations.store');
    Route::post('public/visa-applications', [VisaApplicationController::class, 'store'])->middleware('throttle:public-submit');
    Route::post('public/visa-applications/{visa_application}/documents', [ApplicationDocumentController::class, 'store'])->middleware('throttle:uploads');
    Route::get('public/invoices/{invoice_number}', [InvoiceController::class, 'publicShow'])->middleware('throttle:public-read')->name('api.v1.public.invoices.show');
    Route::get('public/invoices/{invoice_number}/pdf', [InvoiceController::class, 'publicPdf'])->middleware('throttle:public-read')->name('api.v1.public.invoices.pdf');
    Route::post('public/invoices/{invoice_number}/pay/stripe', [StripeWebhookController::class, 'checkout'])->middleware('throttle:public-submit')->name('api.v1.public.invoices.pay.stripe');
    Route::post('public/invoices/{invoice_number}/pay/tabby', [TabbyWebhookController::class, 'checkout'])->middleware('throttle:public-submit')->name('api.v1.public.invoices.pay.tabby');
    Route::post('public/invoices/{invoice_number}/pay/bank-transfer', [BankTransferPaymentController::class, 'store'])->middleware('throttle:public-submit')->name('api.v1.public.invoices.pay.bank_transfer');

    Route::post('webhooks/stripe', [StripeWebhookController::class, 'webhook'])->name('api.v1.webhooks.stripe');
    Route::post('webhooks/tabby', [TabbyWebhookController::class, 'webhook'])->name('api.v1.webhooks.tabby');

    Route::middleware(['auth:sanctum', 'throttle:admin-api'])->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/profile', [AuthController::class, 'profile']);
        Route::patch('auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('auth/password', [AuthController::class, 'changePassword']);

        Route::prefix('admin')->group(function (): void {
            Route::get('languages', [LanguageController::class, 'index'])->middleware('permission:languages.view');
            Route::post('languages', [LanguageController::class, 'store'])->middleware('permission:languages.create');
            Route::match(['put', 'patch'], 'languages/{language}', [LanguageController::class, 'update'])->middleware('permission:languages.update');
            Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->middleware('permission:languages.delete');

            Route::get('settings', [SettingController::class, 'index'])->middleware('permission:settings.view');
            Route::post('settings', [SettingController::class, 'store'])->middleware('permission:settings.create');
            Route::match(['put', 'patch'], 'settings/{setting}', [SettingController::class, 'update'])->middleware('permission:settings.update');
            Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->middleware('permission:settings.delete');

            Route::get('media', [MediaController::class, 'index'])->middleware('permission:media.view');
            Route::post('media', [MediaController::class, 'store'])->middleware('permission:media.upload');
            Route::get('media/{media}/download', [MediaController::class, 'download'])->middleware('permission:media.view')->name('api.v1.admin.media.download');
            Route::delete('media/{media}', [MediaController::class, 'destroy'])->middleware('permission:media.delete');

            Route::get('countries', [CountryController::class, 'index'])->middleware('permission:countries.view');
            Route::post('countries', [CountryController::class, 'store'])->middleware('permission:countries.create');
            Route::match(['put', 'patch'], 'countries/{country}', [CountryController::class, 'update'])->middleware('permission:countries.update');
            Route::delete('countries/{country}', [CountryController::class, 'destroy'])->middleware('permission:countries.delete');

            Route::get('visa-services', [VisaServiceController::class, 'index'])->middleware('permission:services.view');
            Route::post('visa-services', [VisaServiceController::class, 'store'])->middleware('permission:services.create');
            Route::match(['put', 'patch'], 'visa-services/{visa_service}', [VisaServiceController::class, 'update'])->middleware('permission:services.update');
            Route::delete('visa-services/{visa_service}', [VisaServiceController::class, 'destroy'])->middleware('permission:services.delete');

            Route::get('blog/categories', [BlogCategoryController::class, 'index'])->middleware('permission:blog.view');
            Route::post('blog/categories', [BlogCategoryController::class, 'store'])->middleware('permission:blog.create');
            Route::match(['put', 'patch'], 'blog/categories/{blog_category}', [BlogCategoryController::class, 'update'])->middleware('permission:blog.update');
            Route::delete('blog/categories/{blog_category}', [BlogCategoryController::class, 'destroy'])->middleware('permission:blog.delete');
            Route::get('blog/tags', [BlogTagController::class, 'index'])->middleware('permission:blog.view');
            Route::post('blog/tags', [BlogTagController::class, 'store'])->middleware('permission:blog.create');
            Route::match(['put', 'patch'], 'blog/tags/{blog_tag}', [BlogTagController::class, 'update'])->middleware('permission:blog.update');
            Route::delete('blog/tags/{blog_tag}', [BlogTagController::class, 'destroy'])->middleware('permission:blog.delete');
            Route::get('blog/posts', [BlogPostController::class, 'index'])->middleware('permission:blog.view');
            Route::post('blog/posts', [BlogPostController::class, 'store'])->middleware('permission:blog.create');
            Route::match(['put', 'patch'], 'blog/posts/{blog_post}', [BlogPostController::class, 'update'])->middleware('permission:blog.update');
            Route::delete('blog/posts/{blog_post}', [BlogPostController::class, 'destroy'])->middleware('permission:blog.delete');

            Route::get('pages', [PageController::class, 'index'])->middleware('permission:pages.view');
            Route::post('pages', [PageController::class, 'store'])->middleware('permission:pages.create');
            Route::match(['put', 'patch'], 'pages/{page}', [PageController::class, 'update'])->middleware('permission:pages.update');
            Route::delete('pages/{page}', [PageController::class, 'destroy'])->middleware('permission:pages.delete');

            Route::get('customers', [CustomerController::class, 'index'])->middleware('permission:customers.view');
            Route::get('customers/{customer}', [CustomerController::class, 'show'])->middleware('permission:customers.view');
            Route::post('customers', [CustomerController::class, 'store'])->middleware('permission:customers.create');
            Route::match(['put', 'patch'], 'customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.update');
            Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete');

            Route::get('consultations', [ConsultationController::class, 'index'])->middleware('permission:consultations.view');
            Route::get('consultations/{consultation}', [ConsultationController::class, 'show'])->middleware('permission:consultations.view');
            Route::patch('consultations/{consultation}', [ConsultationController::class, 'update'])->middleware('permission:consultations.update');
            Route::post('consultations/{consultation}/assign', [ConsultationController::class, 'assign'])->middleware('permission:consultations.assign');
            Route::post('consultations/{consultation}/notes', [ConsultationController::class, 'addNote'])->middleware('permission:notes.create');
            Route::post('consultations/{consultation}/convert-to-application', [ConsultationController::class, 'convert'])->middleware('permission:consultations.convert');
            Route::get('consultations/{consultation}/timeline', [ConsultationController::class, 'timeline'])->middleware('permission:consultations.view');

            Route::get('visa-applications', [VisaApplicationController::class, 'index'])->middleware('permission:applications.view');
            Route::get('visa-applications/{visa_application}', [VisaApplicationController::class, 'show'])->middleware('permission:applications.view');
            Route::patch('visa-applications/{visa_application}', [VisaApplicationController::class, 'update'])->middleware('permission:applications.update');
            Route::post('visa-applications/{visa_application}/assign', [VisaApplicationController::class, 'assign'])->middleware('permission:applications.assign');
            Route::post('visa-applications/{visa_application}/change-status', [VisaApplicationController::class, 'changeStatus'])->middleware('permission:applications.change_status');
            Route::post('visa-applications/{visa_application}/notes', [VisaApplicationController::class, 'addNote'])->middleware('permission:notes.create');
            Route::get('visa-applications/{visa_application}/timeline', [VisaApplicationController::class, 'timeline'])->middleware('permission:applications.view');
            Route::get('visa-applications/{visa_application}/documents', [ApplicationDocumentController::class, 'index'])->middleware('permission:documents.view');
            Route::post('visa-applications/{visa_application}/documents', [ApplicationDocumentController::class, 'store'])->middleware('permission:documents.upload');
            Route::post('application-documents/{application_document}/review', [ApplicationDocumentController::class, 'review'])->middleware('permission:documents.review');

            Route::get('follow-ups', [FollowUpController::class, 'index'])->middleware('permission:follow_ups.view');
            Route::post('consultations/{consultation}/follow-ups', [FollowUpController::class, 'forConsultation'])->middleware('permission:follow_ups.create');
            Route::post('visa-applications/{visa_application}/follow-ups', [FollowUpController::class, 'forApplication'])->middleware('permission:follow_ups.create');
            Route::post('follow-ups/{follow_up}/complete', [FollowUpController::class, 'complete'])->middleware('permission:follow_ups.complete');
            Route::post('follow-ups/{follow_up}/cancel', [FollowUpController::class, 'cancel'])->middleware('permission:follow_ups.update');

            Route::get('invoices', [InvoiceController::class, 'index'])->middleware('permission:invoices.view');
            Route::post('invoices', [InvoiceController::class, 'store'])->middleware('permission:invoices.create');
            Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:invoices.view');
            Route::patch('invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('permission:invoices.update');
            Route::post('invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->middleware('permission:invoices.issue');
            Route::post('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->middleware('permission:invoices.send');
            Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->middleware('permission:invoices.mark_paid');
            Route::get('invoices/{invoice}/timeline', [InvoiceController::class, 'timeline'])->middleware('permission:invoices.view');
            Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->middleware('permission:invoices.download');

            Route::get('payment-transactions', [PaymentTransactionController::class, 'index'])->middleware('permission:payments.view');
            Route::get('payment-transactions/{transaction}', [PaymentTransactionController::class, 'show'])->middleware('permission:payments.view');
            Route::get('bank-transfers', [BankTransferPaymentController::class, 'index'])->middleware('permission:payments.view');
            Route::post('bank-transfers/{transaction}/approve', [BankTransferPaymentController::class, 'approve'])->middleware('permission:payments.approve_bank_transfers')->name('api.v1.admin.bank-transfers.approve');
            Route::post('bank-transfers/{transaction}/reject', [BankTransferPaymentController::class, 'reject'])->middleware('permission:payments.reject_bank_transfers')->name('api.v1.admin.bank-transfers.reject');

            Route::get('credit-notes', [CreditNoteController::class, 'index'])->middleware('permission:credit_notes.view');
            Route::post('credit-notes', [CreditNoteController::class, 'store'])->middleware('permission:credit_notes.create');
            Route::get('credit-notes/{creditNote}', [CreditNoteController::class, 'show'])->middleware('permission:credit_notes.view');
            Route::post('credit-notes/{creditNote}/issue', [CreditNoteController::class, 'issue'])->middleware('permission:credit_notes.issue');
            Route::get('credit-notes/{creditNote}/pdf', [CreditNoteController::class, 'pdf'])->middleware('permission:credit_notes.download');

            Route::get('refund-requests', [RefundRequestController::class, 'index'])->middleware('permission:refunds.view');
            Route::post('refund-requests', [RefundRequestController::class, 'store'])->middleware('permission:refunds.create');
            Route::get('refund-requests/{refundRequest}', [RefundRequestController::class, 'show'])->middleware('permission:refunds.view');
            Route::post('refund-requests/{refundRequest}/approve', [RefundRequestController::class, 'approve'])->middleware('permission:refunds.approve');
            Route::post('refund-requests/{refundRequest}/reject', [RefundRequestController::class, 'reject'])->middleware('permission:refunds.reject');
            Route::post('refund-requests/{refundRequest}/process', [RefundRequestController::class, 'process'])->middleware('permission:refunds.process');

            Route::get('email-templates', [EmailTemplateController::class, 'index'])->middleware('permission:emails.templates.view');
            Route::get('email-templates/{template}', [EmailTemplateController::class, 'show'])->middleware('permission:emails.templates.view');
            Route::patch('email-templates/{template}', [EmailTemplateController::class, 'update'])->middleware('permission:emails.templates.update');
            Route::get('email-logs', [EmailLogController::class, 'index'])->middleware('permission:emails.logs.view');
            Route::get('email-logs/{emailLog}', [EmailLogController::class, 'show'])->middleware('permission:emails.logs.view');
            Route::get('marketing-events', [MarketingEventController::class, 'index'])->middleware('permission:marketing.events.view');
            Route::get('marketing-events/{marketingEvent}', [MarketingEventController::class, 'show'])->middleware('permission:marketing.events.view');

            Route::get('contact-messages', [ContactMessageController::class, 'index'])->middleware('permission:contact_messages.view');
            Route::get('contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->middleware('permission:contact_messages.view');
            Route::patch('contact-messages/{contactMessage}', [ContactMessageController::class, 'update'])->middleware('permission:contact_messages.update');
            Route::post('contact-messages/{contactMessage}/assign', [ContactMessageController::class, 'assign'])->middleware('permission:contact_messages.assign');
            Route::post('contact-messages/{contactMessage}/mark-read', [ContactMessageController::class, 'markRead'])->middleware('permission:contact_messages.update');
            Route::post('contact-messages/{contactMessage}/close', [ContactMessageController::class, 'close'])->middleware('permission:contact_messages.close');

            Route::apiResource('reviews', ReviewController::class)->except(['create', 'edit'])->middleware([
                'index' => 'permission:reviews.view',
                'show' => 'permission:reviews.view',
                'store' => 'permission:reviews.create',
                'update' => 'permission:reviews.update',
                'destroy' => 'permission:reviews.delete',
            ]);
            Route::apiResource('counters', CounterController::class)->except(['create', 'edit'])->middleware([
                'index' => 'permission:counters.view',
                'show' => 'permission:counters.view',
                'store' => 'permission:counters.create',
                'update' => 'permission:counters.update',
                'destroy' => 'permission:counters.delete',
            ]);
            Route::apiResource('team-members', TeamMemberController::class)->parameters(['team-members' => 'teamMember'])->except(['create', 'edit'])->middleware([
                'index' => 'permission:team.view',
                'show' => 'permission:team.view',
                'store' => 'permission:team.create',
                'update' => 'permission:team.update',
                'destroy' => 'permission:team.delete',
            ]);
            Route::apiResource('partners', PartnerController::class)->except(['create', 'edit'])->middleware([
                'index' => 'permission:partners.view',
                'show' => 'permission:partners.view',
                'store' => 'permission:partners.create',
                'update' => 'permission:partners.update',
                'destroy' => 'permission:partners.delete',
            ]);
            Route::apiResource('branches', BranchController::class)->except(['create', 'edit'])->middleware([
                'index' => 'permission:branches.view',
                'show' => 'permission:branches.view',
                'store' => 'permission:branches.create',
                'update' => 'permission:branches.update',
                'destroy' => 'permission:branches.delete',
            ]);

            Route::get('reports/dashboard', DashboardReportController::class)->middleware('permission:reports.dashboard.view');
            Route::get('reports/sales', SalesReportController::class)->middleware('permission:reports.sales.view');
            Route::get('reports/employee-performance', EmployeePerformanceReportController::class)->middleware('permission:reports.employee_performance.view');
        });
    });
});
