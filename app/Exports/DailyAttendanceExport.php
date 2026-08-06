<?php

namespace App\Exports;

use App\Data\Reports\AttendanceReportFilterData;
use App\Services\AttendanceReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyAttendanceExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(
        private AttendanceReportFilterData $filter,
        private AttendanceReportService $reportService
    ) {}

    public function view(): View
    {
        return view('exports.excel.daily', [
            'attendances' => $this->reportService->getDailyAll($this->filter),
            'filter' => $this->filter
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true]],
        ];
    }
}
