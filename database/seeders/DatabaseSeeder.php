<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            PositionSeeder::class,
            WorkScheduleSeeder::class,
            OfficeLocationSeeder::class,
            SystemSettingSeeder::class,
            SuperAdminSeeder::class,
            DemoEmployeeSeeder::class,
        ]);
    }
}