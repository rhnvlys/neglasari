<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Enums\CheckOutStatus;
use App\Enums\LocationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'work_schedule_id',
        'office_location_id',
        'check_in_at',
        'check_out_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_accuracy',
        'check_in_distance',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_accuracy',
        'check_out_distance',
        'check_in_photo_path',
        'check_out_photo_path',
        'attendance_status',
        'check_in_location_status',
        'check_out_location_status',
        'check_out_status',
        'late_minutes',
        'early_leave_minutes',
        'work_duration_minutes',
        'check_in_ip',
        'check_out_ip',
        'check_in_user_agent',
        'check_out_user_agent',
        'notes',
        'verified_by',
        'verified_at',
        'leave_request_id',
        'source',
        'is_administrative',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'verified_at' => 'datetime',
            'check_in_latitude' => 'decimal:8',
            'check_in_longitude' => 'decimal:8',
            'check_in_accuracy' => 'decimal:2',
            'check_in_distance' => 'decimal:2',
            'check_out_latitude' => 'decimal:8',
            'check_out_longitude' => 'decimal:8',
            'check_out_accuracy' => 'decimal:2',
            'check_out_distance' => 'decimal:2',
            'late_minutes' => 'integer',
            'early_leave_minutes' => 'integer',
            'work_duration_minutes' => 'integer',
            'attendance_status' => AttendanceStatus::class,
            'check_in_location_status' => LocationStatus::class,
            'check_out_location_status' => LocationStatus::class,
            'check_out_status' => CheckOutStatus::class,
            'leave_request_id' => 'integer',
            'is_administrative' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
}