<?php

namespace App\Modules\Counters\Models;

use App\Support\Cache\ClearsPublicApiCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counter extends Model
{
    use ClearsPublicApiCache, SoftDeletes;

    protected $fillable = ['key', 'label', 'value', 'suffix', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
