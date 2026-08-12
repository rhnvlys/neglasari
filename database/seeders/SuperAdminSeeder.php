<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['username' => 'superadmin'], [
            'name' => 'Super Administrator IT Desa',
            'email' => 'superadmin@neglasari.desa.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        
        $admin->syncRoles(['Admin', 'Super Admin', 'Admin Desa']);
    }
}