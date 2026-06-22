<?php

use App\Modules\ApplicationDocuments\Controllers\ApplicationDocumentController;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Blog\Controllers\BlogCategoryController;
use App\Modules\Blog\Controllers\BlogPostController;
use App\Modules\Blog\Controllers\BlogTagController;
use App\Modules\Consultations\Controllers\ConsultationController;
use App\Modules\Countries\Controllers\CountryController;
use App\Modules\CRM\Controllers\FollowUpController;
use App\Modules\Customers\Controllers\CustomerController;
use App\Modules\Languages\Controllers\LanguageController;
use App\Modules\Media\Controllers\MediaController;
use App\Modules\Pages\Controllers\PageController;
use App\Modules\Settings\Controllers\SettingController;
use App\Modules\VisaApplications\Controllers\VisaApplicationController;
use App\Modules\VisaServices\Controllers\VisaServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
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

    Route::post('public/consultations', [ConsultationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('api.v1.consultations.store');
    Route::post('public/visa-applications', [VisaApplicationController::class, 'store'])->middleware('throttle:10,1');
    Route::post('public/visa-applications/{visa_application}/documents', [ApplicationDocumentController::class, 'store'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function (): void {
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
        });
    });
});
