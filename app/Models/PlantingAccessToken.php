<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantingAccessToken extends Model
{
    protected $fillable = ['token_hash', 'token_secret', 'label', 'event_date', 'location_name', 'latitude', 'longitude', 'expires_at', 'last_used_at', 'is_active'];

    protected function casts(): array
    {
        return ['token_secret' => 'encrypted', 'event_date' => 'date', 'expires_at' => 'datetime', 'last_used_at' => 'datetime', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'is_active' => 'boolean'];
    }

    public function plantingRecords(): HasMany
    {
        return $this->hasMany(PlantingRecord::class);
    }
}
