<?php

namespace App\Support\Reports;

enum AttendanceReportType: string
{
    case SUMMARY = 'summary';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case EMPLOYEE = 'employee';
    case POSITION = 'position';
    case LATE = 'late';
    case LEAVE_REQUESTS = 'leave_requests';
    case MISSING_CHECKOUT = 'missing_checkout';
    case ADMINISTRATIVE = 'administrative';
    case PERSONAL_HISTORY = 'history';
    
    public function label(): string
    {
        return match($this) {
            self::SUMMARY => 'Ringkasan Kehadiran',
            self::DAILY => 'Laporan Harian',
            self::MONTHLY => 'Laporan Bulanan',
            self::YEARLY => 'Laporan Tahunan',
            self::EMPLOYEE => 'Laporan per Pegawai',
            self::POSITION => 'Laporan per Jabatan',
            self::LATE => 'Laporan Keterlambatan',
            self::LEAVE_REQUESTS => 'Laporan Pengajuan (Izin/Cuti)',
            self::MISSING_CHECKOUT => 'Laporan Belum Check-out',
            self::ADMINISTRATIVE => 'Laporan Kehadiran Administratif',
            self::PERSONAL_HISTORY => 'Riwayat Absensi Pribadi',
        };
    }
}
