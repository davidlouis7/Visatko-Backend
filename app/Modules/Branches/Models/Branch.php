<?php

namespace App\Modules\Branches\Models;

use App\Support\Cache\ClearsPublicApiCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use ClearsPublicApiCache, SoftDeletes;

    protected $fillable = ['name', 'address', 'phone_numbers', 'whatsapp_number', 'email', 'google_maps_url', 'working_hours', 'emirate', 'city', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['phone_numbers' => 'array', 'is_active' => 'boolean'];
    }
}
