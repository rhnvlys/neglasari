<?php

namespace Database\Seeders;

use App\Models\OfficeLocation;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    public function run(): void
    {
        OfficeLocation::updateOrCreate(['name' => 'Kantor Desa Neglasari'], [
            'address' => 'Jl. Raya Neglasari No. 01, Kecamatan Salawu, Kabupaten Tasikmalaya',
            'latitude' => -7.3750000,
            'longitude' => 108.0850000,
            'radius_meters' => 1000,
            'maximum_accuracy_meters' => 1000,
            'requires_photo' => true,
            'allow_outside_radius' => true,
            'requires_outside_verification' => false,
            'is_active' => true,
        ]);
    }
}