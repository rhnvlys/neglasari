<?php

namespace Database\Seeders;

use App\Models\OfficeLocation;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    public function run(): void
    {
        OfficeLocation::firstOrCreate(['name' => 'Kantor Desa Neglasari'], [
            'address' => 'Jl. Desa Neglasari No. 1',
            'latitude' => -6.90389000, // Dummy
            'longitude' => 107.61861000, // Dummy
            'radius_meters' => 50,
            'maximum_accuracy_meters' => 100,
            'requires_photo' => true,
            'allow_outside_radius' => true,
            'requires_outside_verification' => true,
            'is_active' => true,
        ]);
    }
}