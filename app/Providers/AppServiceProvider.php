<?php

namespace App\Providers;

use App\Listeners\LogFulfilmentEvent;
use App\Models\User;
use App\Modules\ApplicationDocuments\Events\DocumentRejected;
use App\Modules\ApplicationDocuments\Events\DocumentUploaded;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use App\Modules\ApplicationDocuments\Policies\ApplicationDocumentPolicy;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use App\Modules\Blog\Policies\BlogCategoryPolicy;
use App\Modules\Blog\Policies\BlogPostPolicy;
use App\Modules\Blog\Policies\BlogTagPolicy;
use App\Modules\Branches\Models\Branch;
use App\Modules\Branches\Policies\BranchPolicy;
use App\Modules\Consultations\Events\ConsultationCreated;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\Consultations\Policies\ConsultationPolicy;
use App\Modules\ContactMessages\Events\ContactMessageCreated;
use App\Modules\ContactMessages\Models\ContactMessage;
use App\Modules\ContactMessages\Policies\ContactMessagePolicy;
use App\Modules\Counters\Models\Counter;
use App\Modules\Counters\Policies\CounterPolicy;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Policies\CountryPolicy;
use App\Modules\CreditNotes\Models\CreditNote;
use App\Modules\CreditNotes\Policies\CreditNotePolicy;
use App\Modules\CRM\Models\FollowUp;
use App\Modules\CRM\Policies\FollowUpPolicy;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Policies\CustomerPolicy;
use App\Modules\Emails\Listeners\SendDomainEmailListener;
use App\Modules\Emails\Models\EmailLog;
use App\Modules\Emails\Models\EmailTemplate;
use App\Modules\Emails\Policies\EmailLogPolicy;
use App\Modules\Emails\Policies\EmailTemplatePolicy;
use App\Modules\Invoices\Events\InvoiceIssued;
use App\Modules\Invoices\Events\InvoicePaid;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Invoices\Policies\InvoicePolicy;
use App\Modules\Languages\Models\Language;
use App\Modules\Languages\Policies\LanguagePolicy;
use App\Modules\Marketing\Listeners\SendDomainMarketingEventListener;
use App\Modules\Marketing\Models\MarketingEvent;
use App\Modules\Marketing\Policies\MarketingEventPolicy;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Policies\MediaPolicy;
use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Policies\PagePolicy;
use App\Modules\Partners\Models\Partner;
use App\Modules\Partners\Policies\PartnerPolicy;
use App\Modules\Payments\Events\BankTransferUploaded;
use App\Modules\Payments\Events\PaymentFailed;
use App\Modules\Payments\Events\PaymentSucceeded;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Payments\Policies\PaymentTransactionPolicy;
use App\Modules\Refunds\Events\RefundApproved;
use App\Modules\Refunds\Events\RefundRequested;
use App\Modules\Refunds\Models\RefundRequest;
use App\Modules\Refunds\Policies\RefundRequestPolicy;
use App\Modules\Reviews\Models\Review;
use App\Modules\Reviews\Policies\ReviewPolicy;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Policies\SettingPolicy;
use App\Modules\Team\Models\TeamMember;
use App\Modules\Team\Policies\TeamMemberPolicy;
use App\Modules\VisaApplications\Events\VisaApplicationCreated;
use App\Modules\VisaApplications\Events\VisaApplicationStatusChanged;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Modules\VisaApplications\Policies\VisaApplicationPolicy;
use App\Modules\VisaServices\Models\VisaService;
use App\Modules\VisaServices\Policies\VisaServicePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Gate::before(fn (User $user): ?bool => $user->hasRole('Super Admin') ? true : null);
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Country::class, CountryPolicy::class);
        Gate::policy(VisaService::class, VisaServicePolicy::class);
        Gate::policy(BlogCategory::class, BlogCategoryPolicy::class);
        Gate::policy(BlogTag::class, BlogTagPolicy::class);
        Gate::policy(BlogPost::class, BlogPostPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Consultation::class, ConsultationPolicy::class);
        Gate::policy(VisaApplication::class, VisaApplicationPolicy::class);
        Gate::policy(ApplicationDocument::class, ApplicationDocumentPolicy::class);
        Gate::policy(FollowUp::class, FollowUpPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(PaymentTransaction::class, PaymentTransactionPolicy::class);
        Gate::policy(CreditNote::class, CreditNotePolicy::class);
        Gate::policy(RefundRequest::class, RefundRequestPolicy::class);
        Gate::policy(EmailTemplate::class, EmailTemplatePolicy::class);
        Gate::policy(EmailLog::class, EmailLogPolicy::class);
        Gate::policy(MarketingEvent::class, MarketingEventPolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(Counter::class, CounterPolicy::class);
        Gate::policy(TeamMember::class, TeamMemberPolicy::class);
        Gate::policy(Partner::class, PartnerPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);

        Relation::morphMap([
            'consultation' => Consultation::class,
            'visa_application' => VisaApplication::class,
            'follow_up' => FollowUp::class,
            'invoice' => Invoice::class,
            'contact_message' => ContactMessage::class,
        ]);

        Event::listen(ConsultationCreated::class, LogFulfilmentEvent::class);
        Event::listen(VisaApplicationCreated::class, LogFulfilmentEvent::class);
        Event::listen(VisaApplicationStatusChanged::class, LogFulfilmentEvent::class);
        Event::listen(DocumentUploaded::class, LogFulfilmentEvent::class);

        foreach ([ConsultationCreated::class, VisaApplicationCreated::class, VisaApplicationStatusChanged::class, DocumentRejected::class, InvoiceIssued::class, InvoicePaid::class, PaymentFailed::class, BankTransferUploaded::class, RefundRequested::class, RefundApproved::class, ContactMessageCreated::class] as $event) {
            Event::listen($event, SendDomainEmailListener::class);
        }

        foreach ([ConsultationCreated::class, VisaApplicationCreated::class, InvoiceIssued::class, PaymentSucceeded::class, ContactMessageCreated::class, RefundRequested::class] as $event) {
            Event::listen($event, SendDomainMarketingEventListener::class);
        }
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('api-login', fn ($request) => [
            Limit::perMinute((int) env('RATE_LIMIT_LOGIN_PER_MINUTE', 5))->by($request->ip().'|'.strtolower((string) $request->input('email'))),
        ]);

        RateLimiter::for('public-submit', fn ($request) => [
            Limit::perMinute((int) env('RATE_LIMIT_PUBLIC_SUBMIT_PER_MINUTE', 10))->by($request->ip()),
        ]);

        RateLimiter::for('public-read', fn ($request) => [
            Limit::perMinute((int) env('RATE_LIMIT_PUBLIC_READ_PER_MINUTE', 120))->by($request->ip()),
        ]);

        RateLimiter::for('admin-api', fn ($request) => [
            Limit::perMinute((int) env('RATE_LIMIT_ADMIN_PER_MINUTE', 300))->by(optional($request->user())->id ?: $request->ip()),
        ]);

        RateLimiter::for('uploads', fn ($request) => [
            Limit::perMinute((int) env('RATE_LIMIT_UPLOADS_PER_MINUTE', 20))->by(optional($request->user())->id ?: $request->ip()),
        ]);
    }
}
