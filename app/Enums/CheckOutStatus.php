<?php

namespace App\Enums;

enum CheckOutStatus: string
{
    case NORMAL = 'normal';
    case EARLY_LEAVE = 'early_leave';
    case OVERTIME = 'overtime';
    case NOT_CHECKED_OUT = 'not_checked_out';

    public function label(): string
    {
        return match($this) {
            self::NORMAL => 'Normal',
            self::EARLY_LEAVE => 'Pulang Awal',
            self::OVERTIME => 'Lembur',
            self::NOT_CHECKED_OUT => 'Belum Absen',
        };
    }
}