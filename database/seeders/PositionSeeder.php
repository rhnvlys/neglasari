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
            ['code' => 'KASI_PEM', 'name' => 'Kepala Seksi Pemerintahan', 'sort_order' => 3],
            ['code' => 'KASI_KES', 'name' => 'Kepala Seksi Kesejahteraan', 'sort_order' => 4],
            ['code' => 'KASI_PEL', 'name' => 'Kepala Seksi Pelayanan', 'sort_order' => 5],
            ['code' => 'KAUR_TU', 'name' => 'Kepala Urusan Tata Usaha dan Umum', 'sort_order' => 6],
            ['code' => 'KAUR_KEU', 'name' => 'Kepala Urusan Keuangan', 'sort_order' => 7],
            ['code' => 'KAUR_PER', 'name' => 'Kepala Urusan Perencanaan', 'sort_order' => 8],
            ['code' => 'KADUS', 'name' => 'Kepala Dusun', 'sort_order' => 9],
            ['code' => 'STAF', 'name' => 'Staf Desa', 'sort_order' => 10],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(['code' => $position['code']], $position);
        }
    }
}