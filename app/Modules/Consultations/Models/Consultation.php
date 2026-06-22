<?php

namespace App\Modules\Consultations\Models;

use App\Models\User;
use App\Modules\Consultations\Enums\ConsultationStatus;
use App\Modules\Countries\Models\Country;
use App\Modules\CRM\Models\Note;
use App\Modules\CRM\Models\Timeline;
use App\Modules\Customers\Models\Customer;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Modules\VisaServices\Models\VisaService;
use Database\Factories\ConsultationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id', 'preferred_destination_country_id', 'preferred_visa_service_id', 'assigned_to', 'converted_application_id',
        'full_name', 'phone', 'whatsapp_number', 'email', 'nationality', 'current_emirate',
        'are_you_residing_in_uae', 'monthly_salary_range', 'salary_transferred_regularly',
        'has_tenancy_contract', 'owns_car', 'has_previous_travel_history', 'previous_visa_refusal',
        'expected_travel_date', 'notes', 'source', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'meta_event_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConsultationStatus::class,
            'are_you_residing_in_uae' => 'boolean',
            'salary_transferred_regularly' => 'boolean',
            'has_tenancy_contract' => 'boolean',
            'owns_car' => 'boolean',
            'has_previous_travel_history' => 'boolean',
            'previous_visa_refusal' => 'boolean',
            'expected_travel_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'preferred_destination_country_id');
    }

    public function preferredService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class, 'preferred_visa_service_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedApplication(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class, 'converted_application_id');
    }

    public function timelines(): MorphMany
    {
        return $this->morphMany(Timeline::class, 'subject');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'subject');
    }

    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    protected static function newFactory(): ConsultationFactory
    {
        return ConsultationFactory::new();
    }
}
