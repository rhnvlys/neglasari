<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveRequestAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_employee_can_only_view_own_leave_requests(): void
    {
        $employeeA = $this->createEmployeeWithRole('Pegawai');
        $employeeB = $this->createEmployeeWithRole('Pegawai');

        $requestA = LeaveRequest::create([
            'employee_id' => $employeeA->id,
            'type' => LeaveRequestType::PERMISSION,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Izin A',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $requestB = LeaveRequest::create([
            'employee_id' => $employeeB->id,
            'type' => LeaveRequestType::PERMISSION,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Izin B',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        // Employee A can view own detail
        $this->actingAs($employeeA->user)
            ->get(route('pegawai.leave-requests.show', $requestA))
            ->assertOk();

        // Employee A cannot view Employee B's detail
        $this->actingAs($employeeA->user)
            ->get(route('pegawai.leave-requests.show', $requestB))
            ->assertForbidden();
    }

    public function test_employee_cannot_approve_own_leave_request(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        // Add approve permission to employee artificially to test self-approval blocking
        $employee->user->givePermissionTo('approve leave requests');

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::PERMISSION,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Self approve attempt',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $this->actingAs($employee->user)
            ->patch(route('admin.leave-requests.approve', $request), [
                'approval_note' => 'Approve me',
            ])
            ->assertForbidden();
    }

    public function test_employee_cannot_cancel_approved_or_rejected_request(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');

        $approvedRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::PERMISSION,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Approved request',
            'status' => LeaveRequestStatus::APPROVED,
            'approved_by' => $admin->user->id,
        ]);

        $this->actingAs($employee->user)
            ->patch(route('pegawai.leave-requests.cancel', $approvedRequest))
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_access_private_attachment(): void
    {
        Storage::fake('private');
        $path = UploadedFile::fake()->create('rahasia.pdf', 100)->store('leave-attachments', 'private');

        $employeeA = $this->createEmployeeWithRole('Pegawai');
        $employeeB = $this->createEmployeeWithRole('Pegawai');

        $request = LeaveRequest::create([
            'employee_id' => $employeeA->id,
            'type' => LeaveRequestType::SICK,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Sakit',
            'attachment_path' => $path,
            'status' => LeaveRequestStatus::PENDING,
        ]);

        // Employee A (owner) can download
        $this->actingAs($employeeA->user)
            ->get(route('pegawai.leave-requests.attachment', $request))
            ->assertOk();

        // Employee B cannot download
        $this->actingAs($employeeB->user)
            ->get(route('pegawai.leave-requests.attachment', $request))
            ->assertForbidden();
    }

    public function test_inactive_employee_user_cannot_create_leave_request(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $employee->user->update(['is_active' => false]);

        $this->actingAs($employee->user)
            ->get(route('pegawai.leave-requests.create'))
            ->assertRedirect(route('login'));
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
