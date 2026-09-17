<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantingRecord extends Model
{
    protected $fillable = ['planting_access_token_id', 'name', 'phone', 'email', 'organization', 'job_title', 'planted_at', 'plant_type', 'tree_count', 'latitude', 'longitude', 'location_name', 'photo_path'];

    protected function casts(): array
    {
        return ['planted_at' => 'date', 'tree_count' => 'integer', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    }

    public function plantingAccessToken(): BelongsTo
    {
        return $this->belongsTo(PlantingAccessToken::class);
    }
}
