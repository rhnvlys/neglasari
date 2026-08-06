<?php

namespace App\Exports;

use App\Data\Reports\AttendanceReportFilterData;
use App\Services\AttendanceReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MissingCheckoutExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(
        private AttendanceReportFilterData $filter,
        private AttendanceReportService $reportService
    ) {}

    public function view(): View
    {
        return view('exports.excel.missing_checkout', [
            'attendances' => $this->reportService->getMissingCheckoutAll($this->filter),
            'filter' => $this->filter
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true]],
            5    => ['font' => ['bold' => true]],
        ];
    }
}
