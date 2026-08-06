<?php

namespace Database\Seeders;

use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'local' && app()->environment() !== 'testing') {
            return;
        }

        $roles = [
            'admin' => ['Admin Desa', 'KAUR_TU'],
            'kades' => ['Kepala Desa', 'KADES'],
            'pegawai' => ['Pegawai', 'STAF'],
        ];

        foreach ($roles as $username => $data) {
            $roleName = $data[0];
            $positionCode = $data[1];
            
            $position = Position::where('code', $positionCode)->first();
            
            if (!$position) continue;

            $employee = Employee::firstOrCreate(['employee_number' => 'EMP-' . strtoupper($username)], [
                'nik' => '320000000000' . rand(1000, 9999),
                'full_name' => 'User ' . $roleName,
                'gender' => Gender::MALE,
                'position_id' => $position->id,
                'employment_status' => EmploymentStatus::PERMANENT,
                'joined_at' => now()->subYears(2),
                'is_active' => true,
            ]);

            $user = User::firstOrCreate(['username' => $username], [
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'email' => "$username.emp@neglasari.desa.id",
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);

            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }
        
        // Buat beberapa pegawai tambahan
        $stafPosition = Position::where('code', 'STAF')->first();
        if ($stafPosition) {
            for ($i=1; $i<=7; $i++) {
                $employee = Employee::firstOrCreate(['employee_number' => 'EMP-100' . $i], [
                    'nik' => '320000000001' . $i,
                    'full_name' => 'Staf Pegawai ' . $i,
                    'gender' => $i % 2 == 0 ? Gender::FEMALE : Gender::MALE,
                    'position_id' => $stafPosition->id,
                    'employment_status' => EmploymentStatus::PERMANENT,
                    'is_active' => true,
                ]);
                
                $user = User::firstOrCreate(['username' => 'staf' . $i], [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]);
                $user->assignRole('Pegawai');
            }
        }
    }
}
