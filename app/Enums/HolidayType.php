<?php

namespace App\Enums;

enum HolidayType: string
{
    case NATIONAL = 'national';
    case JOINT_LEAVE = 'joint_leave';
    case VILLAGE = 'village';
    case SPECIAL = 'special';
    case REPLACEMENT = 'replacement';

    public function label(): string
    {
        return match($this) {
            self::NATIONAL => 'Libur Nasional',
            self::JOINT_LEAVE => 'Cuti Bersama',
            self::VILLAGE => 'Libur Desa',
            self::SPECIAL => 'Kegiatan Khusus',
            self::REPLACEMENT => 'Hari Kerja Pengganti',
        };
    }
}