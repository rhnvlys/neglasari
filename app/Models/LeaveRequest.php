<?php

namespace App\Models;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use App\Notifications\LeaveRequestApprovedNotification;
use App\Notifications\LeaveRequestRejectedNotification;
use App\Notifications\LeaveRequestSubmittedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment_path',
        'status',
        'approved_by',
        'approved_at',
        'approval_note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
            'type' => LeaveRequestType::class,
            'status' => LeaveRequestStatus::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function notifyAdmins(): void
    {
        $admins = User::whereHas('roles', fn ($query) =>
            $query->whereIn('name', ['Super Admin', 'Admin Desa', 'Kepala Desa'])
        )->get();

        Notification::send($admins, new LeaveRequestSubmittedNotification($this));
    }

    public function notifyEmployee(): void
    {
        $notification = match ($this->status) {
            LeaveRequestStatus::APPROVED => new LeaveRequestApprovedNotification($this),
            LeaveRequestStatus::REJECTED => new LeaveRequestRejectedNotification($this),
            default => null,
        };

        if ($notification && $this->employee->user) {
            $this->employee->user->notify($notification);
        }
    }

    public function scopeByStatus($query, LeaveRequestStatus|string $status)
    {
        if ($status instanceof LeaveRequestStatus) {
            $status = $status->value;
        }
        return $query->where('status', $status);
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }
}