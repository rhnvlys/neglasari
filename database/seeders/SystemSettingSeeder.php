<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'app_name', 'value' => 'SIAP Neglasari', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'village_name', 'value' => 'Neglasari', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'district_name', 'value' => 'Kecamatan', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'city_name', 'value' => 'Kabupaten', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'province_name', 'value' => 'Jawa Barat', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'village_head_name', 'value' => 'Nama Kepala Desa', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'village_head_nik', 'value' => '1234567890123456', 'type' => 'string', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}