<?php

namespace App\Http\Controllers\Reports;

use App\Data\Reports\AttendanceReportFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceExportRequest;
use App\Models\ActivityLog;
use App\Services\AttendanceReportService;
use App\Support\Reports\AttendanceReportFilename;
use App\Support\Reports\AttendanceReportType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyAttendanceExport;
use App\Exports\MissingCheckoutExport;
use App\Exports\MonthlyAttendanceExport;

class AttendanceExportController extends Controller
{
    public function __construct(
        private AttendanceReportService $reportService
    ) {}

    public function export(AttendanceExportRequest $request)
    {
        $type = AttendanceReportType::from($request->validated('type'));
        $format = $request->validated('format');
        $filter = AttendanceReportFilterData::fromRequest($request->validated());

        if (!$request->user() || !$request->user()->hasRole(['Admin', 'Super Admin', 'Admin Desa'])) {
            abort(403, 'Fitur ekspor hanya diperuntukkan bagi Admin.');
        }

        if ($format === 'xlsx' && !$request->user()->can('export attendance reports excel')) {
            abort(403);
        }

        if ($format === 'pdf' && !$request->user()->can('export attendance reports pdf')) {
            abort(403);
        }

        ActivityLog::create([
            'action' => "export_{$format}",
            'module' => 'attendance_reports',
            'subject_type' => 'AttendanceReport',
            'subject_id' => 0,
            'user_id' => Auth::id(),
            'description' => "User mengekspor laporan {$type->label()} dalam format {$format}",
            'new_values' => ['filters' => $filter->toArray()],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $filename = AttendanceReportFilename::generate($type, $filter->toArray(), $format);

        if ($format === 'xlsx') {
            return $this->exportExcel($type, $filter, $filename);
        }

        return $this->exportPdf($type, $filter, $filename);
    }

    private function exportExcel(AttendanceReportType $type, AttendanceReportFilterData $filter, string $filename)
    {
        $exportClass = match ($type) {
            AttendanceReportType::DAILY,
            AttendanceReportType::EMPLOYEE,
            AttendanceReportType::POSITION,
            AttendanceReportType::LATE,
            AttendanceReportType::ADMINISTRATIVE,
            AttendanceReportType::LEAVE_REQUESTS => new DailyAttendanceExport($filter, $this->reportService),
            AttendanceReportType::MONTHLY,
            AttendanceReportType::YEARLY,
            AttendanceReportType::SUMMARY => new MonthlyAttendanceExport($filter, $this->reportService),
            AttendanceReportType::MISSING_CHECKOUT => new MissingCheckoutExport($filter, $this->reportService),
        };

        return Excel::download($exportClass, $filename);
    }

    private function exportPdf(AttendanceReportType $type, AttendanceReportFilterData $filter, string $filename)
    {
        $data = match ($type) {
            AttendanceReportType::DAILY => $this->reportService->getDailyAll($filter),
            AttendanceReportType::MONTHLY,
            AttendanceReportType::YEARLY,
            AttendanceReportType::SUMMARY,
            AttendanceReportType::EMPLOYEE,
            AttendanceReportType::POSITION => $this->reportService->getYearlyRecap($filter),
            AttendanceReportType::MISSING_CHECKOUT => $this->reportService->getMissingCheckoutAll($filter),
            AttendanceReportType::LATE => $this->reportService->getLateAll($filter),
            AttendanceReportType::ADMINISTRATIVE => $this->reportService->getAdministrativeAll($filter),
            AttendanceReportType::LEAVE_REQUESTS => $this->reportService->getLeaveRequestsAll($filter),
        };

        $view = in_array($type, [
            AttendanceReportType::MONTHLY,
            AttendanceReportType::YEARLY,
            AttendanceReportType::SUMMARY,
            AttendanceReportType::EMPLOYEE,
            AttendanceReportType::POSITION,
        ]) ? 'reports.pdf.monthly' : 'reports.pdf.daily';

        $pdf = Pdf::loadView($view, [
            'data' => $data,
            'filter' => $filter,
            'type' => $type,
            'title' => $type->label(),
        ])->setPaper('a4', in_array($type, [
            AttendanceReportType::DAILY,
            AttendanceReportType::MISSING_CHECKOUT,
            AttendanceReportType::LATE,
            AttendanceReportType::ADMINISTRATIVE,
            AttendanceReportType::LEAVE_REQUESTS,
        ]) ? 'landscape' : 'portrait');

        return $pdf->download($filename);
    }
}
