<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use App\Notifications\LeaveRequestApprovedNotification;
use App\Notifications\LeaveRequestRejectedNotification;
use App\Notifications\LeaveRequestSubmittedNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeaveRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_new_leave_request_notifies_admin(): void
    {
        Notification::fake();
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        $superAdmin = $this->createEmployeeWithRole('Super Admin');
        $kades = $this->createEmployeeWithRole('Kepala Desa');
        
        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => 'permission',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'reason' => 'Keperluan keluarga',
            'status' => 'pending',
        ]);

        $request->notifyAdmins();

        Notification::assertSentTo(
            [$admin->user, $superAdmin->user, $kades->user],
            LeaveRequestSubmittedNotification::class
        );
    }

    public function test_approved_leave_request_notifies_employee(): void
    {
        Notification::fake();
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        
        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => 'permission',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'reason' => 'Keperluan keluarga',
            'status' => 'approved',
            'approved_by' => $admin->user->id,
            'approval_note' => 'Disetujui',
        ]);

        $request->notifyEmployee();

        Notification::assertSentTo(
            $employee->user,
            LeaveRequestApprovedNotification::class
        );
    }

    public function test_rejected_leave_request_notifies_employee(): void
    {
        Notification::fake();
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        
        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => 'permission',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'reason' => 'Keperluan keluarga',
            'status' => 'rejected',
            'approved_by' => $admin->user->id,
            'approval_note' => 'Tidak diizinkan',
        ]);

        $request->notifyEmployee();

        Notification::assertSentTo(
            $employee->user,
            LeaveRequestRejectedNotification::class
        );
    }

    public function test_notification_data_is_correct(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => 'sick',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'reason' => 'Sakit',
            'status' => 'pending',
        ]);

        $notification = new LeaveRequestSubmittedNotification($request);
        $data = $notification->toArray(new AnonymousNotifiable());

        $this->assertEquals('Pengajuan Izin Baru', $data['title']);
        $this->assertEquals("Pengajuan Sakit dari {$employee->full_name} menunggu persetujuan.", $data['message']);
        $this->assertEquals($request->id, $data['leave_request_id']);
        $this->assertEquals(route('admin.leave-requests.show', $request), $data['action_url']);
    }

    private function createEmployeeWithRole(string $roleName): Employee
    {
        $position = Position::create(['name' => $roleName, 'is_active' => true]);
        $employee = Employee::create([
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'full_name' => 'Test User ' . rand(1, 100),
            'gender' => 'male',
            'position_id' => $position->id,
            'employment_status' => 'permanent',
            'is_active' => true,
        ]);
        $user = User::create([
            'employee_id' => $employee->id,
            'name' => $employee->full_name,
            'username' => 'user' . $employee->id,
            'email' => "user{$employee->id}@example.test",
            'password' => 'password123',
            'is_active' => true,
        ]);
        $user->assignRole($roleName);

        return $employee->setRelation('user', $user);
    }
}
