<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case LATE = 'late';
    case PERMISSION = 'permission';
    case SICK = 'sick';
    case LEAVE = 'leave';
    case OFFICIAL_DUTY = 'official_duty';
    case FIELD_ASSIGNMENT = 'field_assignment';
    case ABSENT = 'absent';
    case HOLIDAY = 'holiday';

    public function label(): string
    {
        return match($this) {
            self::PRESENT => 'Hadir',
            self::LATE => 'Terlambat',
            self::PERMISSION => 'Izin',
            self::SICK => 'Sakit',
            self::LEAVE => 'Cuti',
            self::OFFICIAL_DUTY => 'Dinas Luar',
            self::FIELD_ASSIGNMENT => 'Tugas Lapangan',
            self::ABSENT => 'Alpa',
            self::HOLIDAY => 'Libur',
        };
    }
}