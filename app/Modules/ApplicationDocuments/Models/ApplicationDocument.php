<?php

namespace App\Modules\ApplicationDocuments\Models;

use App\Models\User;
use App\Modules\ApplicationDocuments\Enums\DocumentStatus;
use App\Modules\ApplicationDocuments\Enums\DocumentType;
use App\Modules\Customers\Models\Customer;
use App\Modules\Media\Models\Media;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    protected $fillable = ['visa_application_id', 'customer_id', 'media_id', 'document_type', 'title', 'status', 'rejection_reason', 'uploaded_by', 'reviewed_by', 'reviewed_at'];

    protected function casts(): array
    {
        return ['document_type' => DocumentType::class, 'status' => DocumentStatus::class, 'reviewed_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class, 'visa_application_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
