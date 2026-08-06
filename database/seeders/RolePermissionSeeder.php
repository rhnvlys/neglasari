<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Admin Permissions
        $permissions = [
            'access admin',
            'manage employees',
            'manage positions',
            'manage users',
            'manage schedules',
            'manage holidays',
            'manage locations',
            'manage settings',
            'view attendances',
            'correct attendances',
            'process leave requests',
            'view reports',
            'view audit logs',
            // Leave Request Permissions
            'view own leave requests',
            'create leave requests',
            'cancel own leave requests',
            'view all leave requests',
            'approve leave requests',
            'reject leave requests',
            'view leave request attachments',
            // Laporan Absensi & Ekspor
            'view attendance reports',
            'view executive attendance reports',
            'export attendance reports excel',
            'export attendance reports pdf',
            'print attendance reports',
            'view own attendance report',
            'export own attendance report',
            'view attendance photos in reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin Desa']);
        $admin->givePermissionTo([
            'access admin',
            'manage employees',
            'manage positions',
            'manage users',
            'manage schedules',
            'manage holidays',
            'view attendances',
            'correct attendances',
            'process leave requests',
            'view reports',
            // Leave Request Permissions
            'view all leave requests',
            'approve leave requests',
            'reject leave requests',
            'view leave request attachments',
            // Laporan
            'view attendance reports',
            'export attendance reports excel',
            'export attendance reports pdf',
            'print attendance reports',
            'view attendance photos in reports',
        ]);

        $kepalaDesa = Role::firstOrCreate(['name' => 'Kepala Desa']);
        $kepalaDesa->givePermissionTo([
            'access admin',
            'view attendances',
            'process leave requests',
            'view reports',
            // Leave Request Permissions
            'view all leave requests',
            'approve leave requests',
            'reject leave requests',
            'view leave request attachments',
            // Laporan
            'view executive attendance reports',
            'export attendance reports excel',
            'export attendance reports pdf',
            'print attendance reports',
        ]);

        $pegawai = Role::firstOrCreate(['name' => 'Pegawai']);
        $pegawai->givePermissionTo([
            'view own leave requests',
            'create leave requests',
            'cancel own leave requests',
            'view own attendance report',
            'export own attendance report',
        ]);
    }
}