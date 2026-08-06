<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficeLocation;
use App\Models\Setting;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $today = Carbon::today();
        $officeLocation = OfficeLocation::where('is_active', true)->first();
        
        $stats = [
            'present' => Attendance::where('attendance_date', $today)
                ->where('attendance_status', 'present')
                ->count(),
            'late' => Attendance::where('attendance_date', $today)
                ->where('attendance_status', 'late')
                ->count(),
            'absent' => Employee::where('is_active', true)
                ->whereDoesntHave('attendances', function($query) use ($today) {
                    $query->where('attendance_date', $today);
                })
                ->count(),
            'leave' => Attendance::where('attendance_date', $today)
                ->whereIn('attendance_status', ['permission', 'sick', 'leave', 'official_duty', 'field_assignment'])
                ->count(),
        ];
        
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $pendingLeaves = LeaveRequest::where('status', 'pending')
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentActivities', 'pendingLeaves', 'officeLocation'));
    }

    public function employee()
    {
        $today = Carbon::today();
        $employee = Auth::user()->employee;
        $officeLocation = OfficeLocation::where('is_active', true)->first();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();
        
        $monthlyStats = [
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('attendance_status', 'present')
                ->count(),
            'late' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('attendance_status', 'late')
                ->count(),
            'leave' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->whereIn('attendance_status', ['permission', 'sick', 'leave', 'official_duty', 'field_assignment'])
                ->count(),
        ];
        
        $pendingLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('employee.dashboard', compact('attendance', 'monthlyStats', 'officeLocation', 'pendingLeaves'));
    }

    public function kades()
    {
        $today = Carbon::today();
        $officeLocation = OfficeLocation::where('is_active', true)->first();
        
        $monthlyStats = [
            'present' => Attendance::whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('attendance_status', 'present')
                ->count(),
            'late' => Attendance::whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('attendance_status', 'late')
                ->count(),
            'absent' => Attendance::whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('attendance_status', 'absent')
                ->count(),
            'leave' => Attendance::whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->whereIn('attendance_status', ['permission', 'sick', 'leave', 'official_duty', 'field_assignment'])
                ->count(),
        ];
        
        $pendingLeaves = LeaveRequest::where('status', 'pending')
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('kades.dashboard', compact('monthlyStats', 'pendingLeaves', 'recentActivities', 'officeLocation'));
    }
}
