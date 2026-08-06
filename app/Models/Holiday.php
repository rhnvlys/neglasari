<?php

namespace App\Models;

use App\Enums\HolidayType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'description',
        'applies_to_all',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'applies_to_all' => 'boolean',
            'is_active' => 'boolean',
            'type' => HolidayType::class,
        ];
    }
    
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }
}