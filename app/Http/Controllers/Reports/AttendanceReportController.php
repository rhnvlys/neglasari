<?php

namespace App\Http\Controllers\Reports;

use App\Data\Reports\AttendanceReportFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceReportRequest;
use App\Services\AttendanceReportService;
use App\Models\Position;
use App\Models\Employee;

class AttendanceReportController extends Controller
{
    public function __construct(
        private AttendanceReportService $reportService
    ) {}

    public function daily(AttendanceReportRequest $request)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());
        $attendances = $this->reportService->getDaily($filter);
        
        return view('reports.daily', [
            'attendances' => $attendances,
            'filter' => $filter->toArray(),
            'positions' => Position::where('is_active', true)->get(),
            'employees' => Employee::where('is_active', true)->get()
        ]);
    }

    public function monthly(AttendanceReportRequest $request)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());
        
        // Default to current month if empty
        if (!$filter->month || !$filter->year) {
            $filter = new AttendanceReportFilterData(
                month: $filter->month ?? now()->month,
                year: $filter->year ?? now()->year,
                employee_id: $filter->employee_id,
                position_id: $filter->position_id,
                keyword: $filter->keyword
            );
        }

        $summary = $this->reportService->getMonthlyRecap($filter);
        
        return view('reports.monthly', [
            'summary' => $summary,
            'filter' => $filter->toArray(),
            'positions' => Position::where('is_active', true)->get(),
            'employees' => Employee::where('is_active', true)->get()
        ]);
    }

    public function missingCheckout(AttendanceReportRequest $request)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());
        $attendances = $this->reportService->getMissingCheckout($filter);
        
        return view('reports.missing_checkout', [
            'attendances' => $attendances,
            'filter' => $filter->toArray(),
            'positions' => Position::where('is_active', true)->get(),
            'employees' => Employee::where('is_active', true)->get()
        ]);
    }

    public function yearly(AttendanceReportRequest $request)
    {
        return $this->recap($request, 'yearly', 'Laporan Tahunan');
    }

    public function employee(AttendanceReportRequest $request)
    {
        return $this->recap($request, 'employee', 'Laporan per Pegawai');
    }

    public function position(AttendanceReportRequest $request)
    {
        return $this->recap($request, 'position', 'Laporan per Jabatan');
    }

    public function summary(AttendanceReportRequest $request)
    {
        return $this->recap($request, 'summary', 'Ringkasan Kehadiran');
    }

    public function late(AttendanceReportRequest $request)
    {
        return $this->records($request, 'late', 'Laporan Keterlambatan');
    }

    public function administrative(AttendanceReportRequest $request)
    {
        return $this->records($request, 'administrative', 'Laporan Kehadiran Administratif');
    }

    public function leaveRequests(AttendanceReportRequest $request)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());

        return view('reports.records', $this->viewData(
            $filter,
            $this->reportService->getLeaveRequests($filter),
            'leave_requests',
            'Laporan Pengajuan Izin/Cuti',
            true
        ));
    }

    public function history(AttendanceReportRequest $request)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());

        return view('reports.records', $this->viewData(
            $filter,
            $this->reportService->getDaily($filter),
            'history',
            'Riwayat Absensi Saya'
        ));
    }

    private function recap(AttendanceReportRequest $request, string $type, string $title)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());

        return view('reports.recap', $this->viewData(
            $filter,
            $this->reportService->getYearlyRecap($filter),
            $type,
            $title
        ));
    }

    private function records(AttendanceReportRequest $request, string $type, string $title)
    {
        $filter = AttendanceReportFilterData::fromRequest($request->validated());

        return view('reports.records', $this->viewData(
            $filter,
            $type === 'late'
                ? $this->reportService->getLate($filter)
                : $this->reportService->getAdministrative($filter),
            $type,
            $title
        ));
    }

    private function viewData(
        AttendanceReportFilterData $filter,
        mixed $data,
        string $type,
        string $title,
        bool $leaveRequest = false
    ): array {
        $user = auth()->user();
        $canViewAll = $user && ($user->can('view attendance reports') || $user->can('view executive attendance reports'));

        return [
            'data' => $data,
            'filter' => $filter->toArray(),
            'type' => $type,
            'title' => $title,
            'leaveRequest' => $leaveRequest,
            'positions' => Position::where('is_active', true)->orderBy('sort_order')->get(),
            'employees' => $canViewAll 
                ? Employee::where('is_active', true)->orderBy('full_name')->get()
                : ($user && $user->employee_id ? Employee::where('id', $user->employee_id)->get() : collect()),
        ];
    }
}
