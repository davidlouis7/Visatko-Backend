<?php

namespace App\Modules\Customers\Models;

use App\Modules\Consultations\Models\Consultation;
use App\Modules\VisaApplications\Models\VisaApplication;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['full_name', 'email', 'phone', 'whatsapp_number', 'nationality', 'residence_country', 'emirate', 'preferred_language', 'source', 'notes', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function visaApplications(): HasMany
    {
        return $this->hasMany(VisaApplication::class);
    }
}
