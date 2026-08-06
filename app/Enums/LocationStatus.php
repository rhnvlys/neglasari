<?php

namespace App\Enums;

enum LocationStatus: string
{
    case INSIDE_RADIUS = 'inside_radius';
    case OUTSIDE_RADIUS = 'outside_radius';
    case LOCATION_UNAVAILABLE = 'location_unavailable';
    case PENDING_VERIFICATION = 'pending_verification';

    public function label(): string
    {
        return match($this) {
            self::INSIDE_RADIUS => 'Dalam Radius',
            self::OUTSIDE_RADIUS => 'Luar Radius',
            self::LOCATION_UNAVAILABLE => 'Lokasi Tidak Tersedia',
            self::PENDING_VERIFICATION => 'Menunggu Verifikasi',
        };
    }
}