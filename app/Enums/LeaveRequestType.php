<?php

namespace App\Enums;

enum LeaveRequestType: string
{
    case PERMISSION = 'permission';
    case SICK = 'sick';
    case LEAVE = 'leave';
    case OFFICIAL_DUTY = 'official_duty';
    case FIELD_ASSIGNMENT = 'field_assignment';

    public function label(): string
    {
        return match($this) {
            self::PERMISSION => 'Izin',
            self::SICK => 'Sakit',
            self::LEAVE => 'Cuti',
            self::OFFICIAL_DUTY => 'Dinas Luar',
            self::FIELD_ASSIGNMENT => 'Tugas Lapangan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PERMISSION => 'bg-blue-100 text-blue-800',
            self::SICK => 'bg-orange-100 text-orange-800',
            self::LEAVE => 'bg-purple-100 text-purple-800',
            self::OFFICIAL_DUTY => 'bg-indigo-100 text-indigo-800',
            self::FIELD_ASSIGNMENT => 'bg-cyan-100 text-cyan-800',
        };
    }
}