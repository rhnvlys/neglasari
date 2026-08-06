<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'maximum_accuracy_meters',
        'requires_photo',
        'allow_outside_radius',
        'requires_outside_verification',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'radius_meters' => 'integer',
            'maximum_accuracy_meters' => 'integer',
            'requires_photo' => 'boolean',
            'allow_outside_radius' => 'boolean',
            'requires_outside_verification' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
    
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}