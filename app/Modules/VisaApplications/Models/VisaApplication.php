<?php

namespace App\Modules\VisaApplications\Models;

use App\Models\User;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\CRM\Models\Note;
use App\Modules\CRM\Models\Timeline;
use App\Modules\Customers\Models\Customer;
use App\Modules\VisaApplications\Enums\ApplicationStatus;
use App\Modules\VisaApplications\Enums\PaymentStatus;
use App\Modules\VisaServices\Models\VisaService;
use Database\Factories\VisaApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['application_number', 'customer_id', 'visa_service_id', 'consultation_id', 'assigned_to', 'full_name', 'email', 'phone', 'whatsapp_number', 'nationality', 'residence_country', 'emirate', 'passport_number', 'travel_date', 'status', 'payment_status', 'customer_notes', 'internal_notes', 'source', 'submitted_at', 'completed_at'];

    protected function casts(): array
    {
        return ['status' => ApplicationStatus::class, 'payment_status' => PaymentStatus::class, 'travel_date' => 'date', 'submitted_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    protected static function newFactory(): VisaApplicationFactory
    {
        return VisaApplicationFactory::new();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function timelines(): MorphMany
    {
        return $this->morphMany(Timeline::class, 'subject');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'subject');
    }
}
