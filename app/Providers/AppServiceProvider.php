<?php

namespace App\Providers;

use App\Listeners\LogFulfilmentEvent;
use App\Models\User;
use App\Modules\ApplicationDocuments\Events\DocumentUploaded;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use App\Modules\ApplicationDocuments\Policies\ApplicationDocumentPolicy;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Models\BlogTag;
use App\Modules\Blog\Policies\BlogCategoryPolicy;
use App\Modules\Blog\Policies\BlogPostPolicy;
use App\Modules\Blog\Policies\BlogTagPolicy;
use App\Modules\Consultations\Events\ConsultationCreated;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\Consultations\Policies\ConsultationPolicy;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Policies\CountryPolicy;
use App\Modules\CRM\Models\FollowUp;
use App\Modules\CRM\Policies\FollowUpPolicy;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Policies\CustomerPolicy;
use App\Modules\Languages\Models\Language;
use App\Modules\Languages\Policies\LanguagePolicy;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Policies\MediaPolicy;
use App\Modules\Pages\Models\Page;
use App\Modules\Pages\Policies\PagePolicy;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Policies\SettingPolicy;
use App\Modules\VisaApplications\Events\VisaApplicationCreated;
use App\Modules\VisaApplications\Events\VisaApplicationStatusChanged;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Modules\VisaApplications\Policies\VisaApplicationPolicy;
use App\Modules\VisaServices\Models\VisaService;
use App\Modules\VisaServices\Policies\VisaServicePolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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

        Relation::morphMap([
            'consultation' => Consultation::class,
            'visa_application' => VisaApplication::class,
            'follow_up' => FollowUp::class,
        ]);

        Event::listen(ConsultationCreated::class, LogFulfilmentEvent::class);
        Event::listen(VisaApplicationCreated::class, LogFulfilmentEvent::class);
        Event::listen(VisaApplicationStatusChanged::class, LogFulfilmentEvent::class);
        Event::listen(DocumentUploaded::class, LogFulfilmentEvent::class);
    }
}
