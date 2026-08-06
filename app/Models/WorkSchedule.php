<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day_of_week',
        'check_in_start',
        'check_in_time',
        'check_in_end',
        'late_tolerance_minutes',
        'check_out_start',
        'check_out_time',
        'check_out_end',
        'is_workday',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_workday' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'late_tolerance_minutes' => 'integer',
        ];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_schedules')
            ->withPivot(['effective_start_date', 'effective_end_date'])
            ->withTimestamps();
    }
    
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}