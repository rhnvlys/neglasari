<?php

namespace App\Services;

use App\Data\Reports\AttendanceReportFilterData;
use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceReportService
{
    public function buildQuery(AttendanceReportFilterData $filter): Builder
    {
        $query = Attendance::with(['employee.position', 'workSchedule', 'officeLocation']);

        if ($filter->date) {
            $query->whereDate('attendance_date', $filter->date);
        }

        if ($filter->start_date && $filter->end_date) {
            $query->whereBetween('attendance_date', [$filter->start_date, $filter->end_date]);
        }

        if ($filter->month && $filter->year) {
            $query->whereMonth('attendance_date', $filter->month)
                  ->whereYear('attendance_date', $filter->year);
        } elseif ($filter->year) {
            $query->whereYear('attendance_date', $filter->year);
        }

        if ($filter->employee_id) {
            $query->where('employee_id', $filter->employee_id);
        }

        if ($filter->position_id) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('position_id', $filter->position_id);
            });
        }

        if ($filter->status) {
            $query->where('attendance_status', $filter->status);
        }

        if ($filter->source) {
            $query->where('source', $filter->source);
        }

        if ($filter->keyword) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('full_name', 'like', '%' . $filter->keyword . '%')
                  ->orWhere('employee_number', 'like', '%' . $filter->keyword . '%')
                  ->orWhere('nik', 'like', '%' . $filter->keyword . '%');
            });
        }

        return $query;
    }
    
    public function getDaily(AttendanceReportFilterData $filter)
    {
        $query = $this->buildQuery($filter);
        return $query->orderBy('attendance_date', 'desc')
                     ->orderBy(Employee::select('full_name')
                        ->whereColumn('employees.id', 'attendances.employee_id')
                        ->limit(1)
                     )
                     ->paginate(50)->withQueryString();
    }
    
    public function getDailyAll(AttendanceReportFilterData $filter)
    {
        return $this->buildQuery($filter)
                    ->orderBy('attendance_date', 'desc')
                    ->orderBy(Employee::select('full_name')
                        ->whereColumn('employees.id', 'attendances.employee_id')
                        ->limit(1)
                    )
                    ->get();
    }
    
    public function getMonthlyRecap(AttendanceReportFilterData $filter)
    {
        // Monthly stats per employee
        $attendances = $this->buildQuery($filter)->get();
        return $this->summarizeByEmployee($attendances, $filter);
    }
    
    public function getYearlyRecap(AttendanceReportFilterData $filter)
    {
        // We aggregate by month or just return the grand summary
        $attendances = $this->buildQuery($filter)->get();
        return $this->summarizeByEmployee($attendances, $filter);
    }

    public function getMissingCheckout(AttendanceReportFilterData $filter)
    {
        $query = clone $this->buildQuery($filter);
        return $query->whereNotNull('check_in_at')
                     ->whereNull('check_out_at')
                     ->where('is_administrative', false)
                     ->whereNotIn('attendance_status', [
                         AttendanceStatus::PERMISSION->value,
                         AttendanceStatus::SICK->value,
                         AttendanceStatus::LEAVE->value,
                         AttendanceStatus::OFFICIAL_DUTY->value,
                         AttendanceStatus::FIELD_ASSIGNMENT->value
                     ])
                     ->orderBy('attendance_date', 'desc')
                     ->paginate(50)->withQueryString();
    }
    
    public function getMissingCheckoutAll(AttendanceReportFilterData $filter)
    {
        $query = clone $this->buildQuery($filter);
        return $query->whereNotNull('check_in_at')
                     ->whereNull('check_out_at')
                     ->where('is_administrative', false)
                     ->whereNotIn('attendance_status', [
                         AttendanceStatus::PERMISSION->value,
                         AttendanceStatus::SICK->value,
                         AttendanceStatus::LEAVE->value,
                         AttendanceStatus::OFFICIAL_DUTY->value,
                         AttendanceStatus::FIELD_ASSIGNMENT->value
                     ])
                     ->orderBy('attendance_date', 'desc')
                     ->get();
    }

    public function getLate(AttendanceReportFilterData $filter)
    {
        $query = clone $this->buildQuery($filter);
        return $query->where('attendance_status', AttendanceStatus::LATE->value)
                     ->orderBy('attendance_date', 'desc')
                     ->paginate(50)->withQueryString();
    }

    public function getLateAll(AttendanceReportFilterData $filter)
    {
        $query = clone $this->buildQuery($filter);
        return $query->where('attendance_status', AttendanceStatus::LATE->value)
                     ->orderBy('attendance_date', 'desc')
                     ->get();
    }

    public function getAdministrative(AttendanceReportFilterData $filter)
    {
        $query = clone $this->buildQuery($filter);
        return $query->where('is_administrative', true)
                     ->orderBy('attendance_date', 'desc')
                     ->paginate(50)->withQueryString();
    }

    public function getAdministrativeAll(AttendanceReportFilterData $filter)
    {
        $query = clone $this->buildQuery($filter);
        return $query->where('is_administrative', true)
                     ->orderBy('attendance_date', 'desc')
                     ->get();
    }

    public function getLeaveRequests(AttendanceReportFilterData $filter)
    {
        $query = LeaveRequest::with(['employee.position', 'approver']);

        if ($filter->start_date && $filter->end_date) {
            $query->byPeriod($filter->start_date, $filter->end_date);
        }

        if ($filter->employee_id) {
            $query->byEmployee($filter->employee_id);
        }

        if ($filter->position_id) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('position_id', $filter->position_id);
            });
        }

        if ($filter->status) {
            $query->byStatus($filter->status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();
    }

    public function getLeaveRequestsAll(AttendanceReportFilterData $filter)
    {
        $query = LeaveRequest::with(['employee.position', 'approver']);

        if ($filter->start_date && $filter->end_date) {
            $query->byPeriod($filter->start_date, $filter->end_date);
        }

        if ($filter->employee_id) {
            $query->byEmployee($filter->employee_id);
        }

        if ($filter->position_id) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('position_id', $filter->position_id);
            });
        }

        if ($filter->status) {
            $query->byStatus($filter->status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
    


    private function summarizeByEmployee($attendances, AttendanceReportFilterData $filter)
    {
        $grouped = $attendances->groupBy('employee_id');
        $summary = [];

        // Determine date range for effective days
        $start = null;
        $end = null;
        if ($filter->start_date && $filter->end_date) {
            $start = Carbon::parse($filter->start_date);
            $end = Carbon::parse($filter->end_date);
        } elseif ($filter->month && $filter->year) {
            $start = Carbon::create($filter->year, $filter->month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            if ($end->isFuture()) {
                $end = now();
            }
        } elseif ($filter->year) {
            $start = Carbon::create($filter->year, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();
            if ($end->isFuture()) {
                $end = now();
            }
        } else {
            // Default 30 days if no date bound provided
            $end = now();
            $start = now()->subDays(30);
        }
        
        // Cache holidays for performance
        $holidays = Holiday::where('is_active', true)
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
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

        // We fetch all active employees if they have no attendances? 
        // Requirements say we must show them even if 0 attendances. 
        // We get employees that match filters
        $employeeQuery = Employee::with('position');
        if ($filter->employee_id) $employeeQuery->where('id', $filter->employee_id);
        if ($filter->position_id) $employeeQuery->where('position_id', $filter->position_id);
        if ($filter->keyword) {
             $employeeQuery->where(function($q) use ($filter) {
                  $q->where('full_name', 'like', '%' . $filter->keyword . '%')
                    ->orWhere('employee_number', 'like', '%' . $filter->keyword . '%')
                    ->orWhere('nik', 'like', '%' . $filter->keyword . '%');
             });
        }
        $employees = $employeeQuery->get();

        foreach ($employees as $employee) {
            $empAttendances = $grouped->get($employee->id, collect());
            
            $effectiveDays = $this->calculateEffectiveDays($start, $end, $employee, $holidayDates);
            
            $presentTime = $empAttendances->where('attendance_status', AttendanceStatus::PRESENT)->count();
            $late = $empAttendances->where('attendance_status', AttendanceStatus::LATE)->count();
            
            $permission = $empAttendances->where('attendance_status', AttendanceStatus::PERMISSION)->count();
            $sick = $empAttendances->where('attendance_status', AttendanceStatus::SICK)->count();
            $leave = $empAttendances->where('attendance_status', AttendanceStatus::LEAVE)->count();
            
            $dinasLuar = $empAttendances->where('attendance_status', AttendanceStatus::OFFICIAL_DUTY)->count();
            $tugasLapangan = $empAttendances->where('attendance_status', AttendanceStatus::FIELD_ASSIGNMENT)->count();
            
            // Kehadiran Fisik = hadir tepat waktu + terlambat
            $physicalPresent = $presentTime + $late;
            
            // Kehadiran Administratif = hadir tepat waktu + terlambat + dinas luar + tugas lapangan
            $adminPresent = $physicalPresent + $dinasLuar + $tugasLapangan;
            
            // Ketidakhadiran Sah
            $validAbsent = $permission + $sick + $leave;
            
            // Alpa = effective - adminPresent - validAbsent (can't be negative)
            $alpa = max(0, $effectiveDays - $adminPresent - $validAbsent);
            
            $physicalPercent = $effectiveDays > 0 ? ($physicalPresent / $effectiveDays) * 100 : 0;
            $adminPercent = $effectiveDays > 0 ? ($adminPresent / $effectiveDays) * 100 : 0;
            $punctuality = $physicalPresent > 0 ? ($presentTime / $physicalPresent) * 100 : 0;

            $missingCheckout = $empAttendances->whereNotNull('check_in_at')->whereNull('check_out_at')->where('is_administrative', false)->count();

            $summary[] = [
                'employee' => $employee,
                'effective_days' => $effectiveDays,
                'present_on_time' => $presentTime,
                'late' => $late,
                'permission' => $permission,
                'sick' => $sick,
                'leave' => $leave,
                'official_duty' => $dinasLuar,
                'field_assignment' => $tugasLapangan,
                'absent' => $alpa,
                'missing_checkout' => $missingCheckout,
                'total_late_minutes' => $empAttendances->sum('late_minutes'),
                'total_early_leave_minutes' => $empAttendances->sum('early_leave_minutes'),
                'total_work_duration' => $empAttendances->sum('work_duration_minutes'),
                'physical_present_count' => $physicalPresent,
                'admin_present_count' => $adminPresent,
                'physical_present_percent' => round($physicalPercent, 2),
                'admin_present_percent' => round($adminPercent, 2),
                'punctuality_percent' => round($punctuality, 2),
            ];
        }

        return collect($summary)->sortByDesc('admin_present_percent')->values();
    }

    private function calculateEffectiveDays(Carbon $start, Carbon $end, Employee $employee, array $holidayDates): int
    {
        $days = 0;
        $current = $start->copy();
        
        $joined = $employee->joined_at ? Carbon::parse($employee->joined_at) : Carbon::create(2000, 1, 1);
        if ($joined->gt($end)) return 0;
        if ($joined->gt($start)) $current = $joined->copy();

        // Get default schedules (simplification for performance)
        $schedules = WorkSchedule::where('is_active', true)->where('is_default', true)->get()->keyBy('day_of_week');

        while ($current->lte($end)) {
            $dateString = $current->toDateString();
            
            if (in_array($dateString, $holidayDates)) {
                $current->addDay();
                continue;
            }
            
            $dayOfWeek = $current->dayOfWeek;
            $schedule = $schedules->get($dayOfWeek);
            
            if ($schedule && $schedule->is_workday) {
                $days++;
            }
            
            $current->addDay();
        }
        
        return $days;
    }
}
