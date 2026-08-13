<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_open_every_management_report(): void
    {
        $admin = $this->createUserWithRole('Admin Desa');
        $employee = $this->createUserWithRole('Pegawai')->employee;

        $routes = [
            'admin.reports.daily',
            'admin.reports.monthly',
            'admin.reports.yearly',
            'admin.reports.employee',
            'admin.reports.position',
            'admin.reports.late',
            'admin.reports.leave-requests',
            'admin.reports.missing-checkout',
            'admin.reports.summary',
            'admin.reports.administrative',
        ];

        foreach ($routes as $route) {
            $parameters = $route === 'admin.reports.employee' ? ['employee_id' => $employee->id] : [];
            $this->actingAs($admin)->get(route($route, $parameters))->assertOk();
        }
    }

    public function test_employee_can_only_open_personal_history_with_forced_ownership(): void
    {
        $employee = $this->createUserWithRole('Pegawai');
        $other = $this->createUserWithRole('Pegawai');

        Attendance::create([
            'employee_id' => $employee->employee_id,
            'attendance_date' => '2026-08-01',
            'attendance_status' => AttendanceStatus::PRESENT,
        ]);
        Attendance::create([
            'employee_id' => $other->employee_id,
            'attendance_date' => '2026-08-01',
            'attendance_status' => AttendanceStatus::LATE,
            'late_minutes' => 15,
        ]);

        $this->actingAs($employee)
            ->get(route('pegawai.reports.history', ['employee_id' => $other->employee_id]))
            ->assertOk()
            ->assertSee($employee->employee->full_name)
            ->assertDontSee($other->employee->full_name);
    }

    public function test_export_requires_permission_for_the_requested_format(): void
    {
        $admin = $this->createUserWithRole('Admin Desa');
        $role = \Spatie\Permission\Models\Role::findByName('Admin Desa');
        $role->revokePermissionTo('export attendance reports pdf');
        $role->revokePermissionTo('export own attendance report');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin)
            ->get(route('admin.reports.export', ['type' => 'daily', 'format' => 'pdf']))
            ->assertForbidden();
    }

    public function test_excel_export_is_downloadable_and_audited(): void
    {
        $admin = $this->createUserWithRole('Admin Desa');

        $response = $this->actingAs($admin)->get(route('admin.reports.export', [
            'type' => 'late',
            'format' => 'xlsx',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-31',
        ]));

        $response->assertOk()->assertDownload();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'export_xlsx',
            'module' => 'attendance_reports',
        ]);
    }

    private function createUserWithRole(string $role): User
    {
        $position = Position::create([
            'name' => $role.' '.uniqid(),
            'is_active' => true,
        ]);
        $employee = Employee::create([
            'employee_number' => 'EMP-'.uniqid(),
            'full_name' => $role.' '.uniqid(),
            'gender' => 'male',
            'position_id' => $position->id,
            'employment_status' => 'permanent',
            'joined_at' => '2026-01-01',
            'is_active' => true,
        ]);
        $user = User::create([
            'employee_id' => $employee->id,
            'name' => $employee->full_name,
            'username' => 'user-'.uniqid(),
            'email' => uniqid().'@example.test',
            'password' => 'password123',
            'is_active' => true,
        ]);
        $user->assignRole($role);

        return $user->setRelation('employee', $employee);
    }
}
