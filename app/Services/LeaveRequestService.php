<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveRequestStatus;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\WorkSchedule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LeaveRequestService
{
    public function create(Employee $employee, array $data, ?UploadedFile $attachment = null): LeaveRequest
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $this->checkOverlap($employee, $startDate, $endDate);

        $path = null;
        if ($attachment) {
            $path = $attachment->store('leave-attachments', 'private');
        }

        return DB::transaction(function () use ($employee, $data, $path) {
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $employee->id,
                'type' => $data['type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'reason' => $data['reason'],
                'attachment_path' => $path,
                'status' => LeaveRequestStatus::PENDING,
            ]);

            $this->logActivity('create', 'Pegawai membuat pengajuan ' . $leaveRequest->type->label(), $leaveRequest);
            $leaveRequest->notifyAdmins();

            return $leaveRequest;
        });
    }

    public function cancel(LeaveRequest $leaveRequest): void
    {
        DB::transaction(function () use ($leaveRequest) {
            $leaveRequest->update(['status' => LeaveRequestStatus::CANCELLED]);
            $this->logActivity('cancel', 'Pegawai membatalkan pengajuan ' . $leaveRequest->type->label(), $leaveRequest);
        });
    }

    public function approve(LeaveRequest $leaveRequest, ?string $note = null): void
    {
        DB::transaction(function () use ($leaveRequest, $note) {
            $leaveRequest->update([
                'status' => LeaveRequestStatus::APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_note' => $note,
            ]);

            $this->syncAttendance($leaveRequest);

            $this->logActivity('approve', 'Admin menyetujui pengajuan ' . $leaveRequest->type->label(), $leaveRequest);
            $leaveRequest->notifyEmployee();
        });
    }

    public function reject(LeaveRequest $leaveRequest, string $note): void
    {
        DB::transaction(function () use ($leaveRequest, $note) {
            $leaveRequest->update([
                'status' => LeaveRequestStatus::REJECTED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_note' => $note,
            ]);

            $this->logActivity('reject', 'Admin menolak pengajuan ' . $leaveRequest->type->label(), $leaveRequest);
            $leaveRequest->notifyEmployee();
        });
    }

    private function checkOverlap(Employee $employee, Carbon $startDate, Carbon $endDate, ?int $excludeId = null): void
    {
        $overlap = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', [LeaveRequestStatus::PENDING->value, LeaveRequestStatus::APPROVED->value])
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'Rentang tanggal ini tumpang tindih dengan pengajuan lain yang sedang pending atau disetujui.',
            ]);
        }
    }

    private function syncAttendance(LeaveRequest $leaveRequest): void
    {
        $startDate = $leaveRequest->start_date;
        $endDate = $leaveRequest->end_date;
        $employee = $leaveRequest->employee;

        // Mendapatkan status absensi dari jenis izin
        $attendanceStatus = AttendanceStatus::from($leaveRequest->type->value);
        
        $holidays = Holiday::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where('is_active', true)
            ->get();
            
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $hStart = Carbon::parse($holiday->start_date);
            $hEnd = Carbon::parse($holiday->end_date);
            while ($hStart->lte($hEnd)) {
                $holidayDates[] = $hStart->toDateString();
                $hStart->addDay();
            }
        }

        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            
            // Skip hari libur
            if (in_array($dateString, $holidayDates)) {
                $currentDate->addDay();
                continue;
            }

            // Cari jadwal
            $dayOfWeek = $currentDate->dayOfWeek;
            $schedule = $employee->workSchedules()->where('day_of_week', $dayOfWeek)->where('is_active', true)->first()
                ?? WorkSchedule::where('day_of_week', $dayOfWeek)->where('is_default', true)->where('is_active', true)->first();

            // Skip hari non-kerja
            if (!$schedule || !$schedule->is_workday) {
                $currentDate->addDay();
                continue;
            }

            // Periksa jika sudah ada absensi (termasuk check_in riil)
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('attendance_date', $dateString)
                ->first();

            if ($existingAttendance) {
                // Jika sudah ada check_in_at, tandai konflik di log, jangan timpa
                if ($existingAttendance->check_in_at) {
                    $this->logActivity('conflict', 'Konflik sinkronisasi absensi pada tanggal ' . $dateString . '. Pegawai sudah melakukan check-in nyata.', $leaveRequest);
                } else {
                    // Update yang ada jika hanya administratif
                    $existingAttendance->update([
                        'attendance_status' => $attendanceStatus,
                        'notes' => 'Otomatis dari pengajuan: ' . $leaveRequest->type->label(),
                        'leave_request_id' => $leaveRequest->id,
                        'source' => 'leave_request',
                        'is_administrative' => true,
                    ]);
                }
            } else {
                // Buat data absensi administratif
                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $dateString,
                    'work_schedule_id' => $schedule->id,
                    'attendance_status' => $attendanceStatus,
                    'notes' => 'Otomatis dari pengajuan: ' . $leaveRequest->type->label(),
                    'leave_request_id' => $leaveRequest->id,
                    'source' => 'leave_request',
                    'is_administrative' => true,
                ]);
            }

            $currentDate->addDay();
        }
    }

    private function logActivity(string $action, string $description, LeaveRequest $leaveRequest): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => 'leave_request',
            'description' => $description,
            'subject_type' => LeaveRequest::class,
            'subject_id' => $leaveRequest->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
