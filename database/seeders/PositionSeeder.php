<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['code' => 'KADES', 'name' => 'Kepala Desa', 'sort_order' => 1],
            ['code' => 'SEKDES', 'name' => 'Sekretaris Desa', 'sort_order' => 2],
            ['code' => 'KAUR_TU', 'name' => 'Kepala Urusan Tata Usaha dan Umum', 'sort_order' => 3],
            ['code' => 'KAUR_PER', 'name' => 'Kepala Urusan Perencanaan', 'sort_order' => 4],
            ['code' => 'KAUR_KEU', 'name' => 'Kepala Urusan Keuangan', 'sort_order' => 5],
            ['code' => 'KASI_PEM', 'name' => 'Kepala Seksi Pemerintahan', 'sort_order' => 6],
            ['code' => 'KASI_PEL', 'name' => 'Kepala Seksi Pelayanan', 'sort_order' => 7],
            ['code' => 'KASI_KES', 'name' => 'Kepala Seksi Kesejahteraan Rakyat', 'sort_order' => 8],
            ['code' => 'KADUS_NANGELA', 'name' => 'Kepala Wilayah Nangela', 'sort_order' => 9],
            ['code' => 'KADUS_GARUNGGANG', 'name' => 'Kepala Wilayah Garunggang', 'sort_order' => 10],
            ['code' => 'KADUS_SANGGARIANG', 'name' => 'Kepala Wilayah Sanggariang', 'sort_order' => 11],
            ['code' => 'KADUS_KARANGSARI', 'name' => 'Kepala Wilayah Karangsari', 'sort_order' => 12],
            ['code' => 'STAF_PEMDES', 'name' => 'Staff Pemerintahan Desa', 'sort_order' => 13],
            ['code' => 'STAF_IT', 'name' => 'Staff IT Desa (Admin)', 'sort_order' => 14],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(['code' => $position['code']], $position);
        }
    }
}