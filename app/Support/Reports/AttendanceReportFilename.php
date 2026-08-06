<?php

namespace App\Support\Reports;

use Illuminate\Support\Str;

class AttendanceReportFilename
{
    public static function generate(AttendanceReportType $type, array $filters, string $extension = 'xlsx'): string
    {
        $parts = ['laporan-absensi', $type->value];

        if (!empty($filters['date'])) {
            $parts[] = $filters['date'];
        } elseif (!empty($filters['year'])) {
            $month = !empty($filters['month']) ? str_pad($filters['month'], 2, '0', STR_PAD_LEFT) : '';
            $parts[] = $month ? "{$filters['year']}-{$month}" : $filters['year'];
        } elseif (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $parts[] = $filters['start_date'] . '-sd-' . $filters['end_date'];
        }

        if (!empty($filters['employee_id'])) {
            // Kita tidak bisa query db di sini dengan bersih, tapi setidaknya tambahkan id atau biarkan controller yang meng-inject nama
            // Untuk sementara kita tambahkan suffix ID. Nama akan di-inject oleh caller jika dibutuhkan.
            if (isset($filters['employee_name'])) {
                 $parts[] = Str::slug($filters['employee_name']);
            } else {
                 $parts[] = 'emp-' . $filters['employee_id'];
            }
        }

        $filename = implode('-', $parts);
        $filename = Str::slug($filename); // Sanitasi
        
        return $filename . '.' . $extension;
    }
}
