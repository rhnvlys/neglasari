<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case PERMANENT = 'permanent';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';

    public function label(): string
    {
        return match($this) {
            self::PERMANENT => 'Tetap',
            self::CONTRACT => 'Kontrak',
            self::INTERNSHIP => 'Magang',
        };
    }
}