<?php

namespace Database\Seeders;

use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;

class WorkScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $weekdays = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
        ];
        
        foreach ($weekdays as $dayNum => $dayName) {
            $isFriday = $dayNum === 5;
            
            WorkSchedule::updateOrCreate([
                'day_of_week' => (string) $dayNum,
            ], [
                'name' => "Reguler - $dayName",
                'check_in_start' => '06:00:00',
                'check_in_time' => '08:00:00',
                'check_in_end' => '10:00:00',
                'late_tolerance_minutes' => 15,
                'check_out_start' => $isFriday ? '14:30:00' : '15:00:00',
                'check_out_time' => $isFriday ? '15:00:00' : '16:00:00',
                'check_out_end' => '20:00:00',
                'is_workday' => true,
                'is_default' => true,
                'is_active' => true,
            ]);
        }
        
        $weekend = [
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        foreach ($weekend as $dayNum => $dayName) {
            WorkSchedule::updateOrCreate([
                'day_of_week' => (string) $dayNum,
            ], [
                'name' => "Libur - $dayName",
                'check_in_start' => '00:00:00',
                'check_in_time' => '00:00:00',
                'check_in_end' => '00:00:00',
                'late_tolerance_minutes' => 0,
                'check_out_start' => '00:00:00',
                'check_out_time' => '00:00:00',
                'check_out_end' => '00:00:00',
                'is_workday' => false,
                'is_default' => true,
                'is_active' => true,
            ]);
        }
    }
}