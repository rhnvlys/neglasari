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

        // System Permissions
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

        // Role 1: Admin (Staff IT Desa / Administrator)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Role 2: Anggota (Perangkat Desa & Staf)
        $anggotaRole = Role::firstOrCreate(['name' => 'Anggota']);
        $anggotaRole->syncPermissions([
            'view own leave requests',
            'create leave requests',
            'cancel own leave requests',
            'view own attendance report',
            'export own attendance report',
        ]);

        // Legacy compatibility aliases if needed by existing guards
        $legacyRoles = [
            'Super Admin' => Permission::all(),
            'Admin Desa' => Permission::all(),
            'Kepala Desa' => Permission::all(),
            'Pegawai' => [
                'view own leave requests',
                'create leave requests',
                'cancel own leave requests',
                'view own attendance report',
                'export own attendance report',
            ]
        ];

        foreach ($legacyRoles as $roleName => $perms) {
            $r = Role::firstOrCreate(['name' => $roleName]);
            $r->syncPermissions($perms);
        }
    }
}