<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['username' => 'superadmin'], [
            'name' => 'Super Administrator',
            'email' => 'admin@neglasari.desa.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        
        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }
    }
}