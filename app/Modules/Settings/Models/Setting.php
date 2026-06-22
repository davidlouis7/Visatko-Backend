<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'is_public', 'is_encrypted'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'is_encrypted' => 'boolean',
        ];
    }

    public function encodeValue(mixed $value): string
    {
        $encoded = match ($this->type) {
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        return $this->is_encrypted ? Crypt::encryptString($encoded) : $encoded;
    }

    public function resolvedValue(): mixed
    {
        $value = $this->is_encrypted ? Crypt::decryptString($this->value) : $this->value;

        return match ($this->type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => $value === '1',
            'json' => json_decode($value, true, flags: JSON_THROW_ON_ERROR),
            default => $value,
        };
    }
}
