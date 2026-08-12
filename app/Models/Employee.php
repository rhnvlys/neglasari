<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_number',
        'nik',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'position_id',
        'phone',
        'email',
        'address',
        'photo_path',
        'joined_at',
        'employment_status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'joined_at' => 'date',
            'is_active' => 'boolean',
            'gender' => Gender::class,
            'employment_status' => EmploymentStatus::class,
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function workSchedules(): BelongsToMany
    {
        return $this->belongsToMany(WorkSchedule::class, 'employee_schedules')
            ->withPivot(['effective_start_date', 'effective_end_date'])
            ->withTimestamps();
    }
    
    public function holidays(): BelongsToMany
    {
        return $this->belongsToMany(Holiday::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}