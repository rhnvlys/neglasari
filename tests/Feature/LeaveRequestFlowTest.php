<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkSchedule;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_employee_can_create_leave_request(): void
    {
        Storage::fake('private');
        $employee = $this->createEmployeeWithRole('Pegawai');
        
        $response = $this->actingAs($employee->user)->post(route('pegawai.leave-requests.store'), [
            'type' => LeaveRequestType::PERMISSION->value,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'reason' => 'Keperluan keluarga',
            'attachment' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf')
        ]);

        $response->assertRedirect(route('pegawai.leave-requests.index'));
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::PERMISSION->value,
            'status' => LeaveRequestStatus::PENDING->value,
        ]);
    }

    public function test_employee_cannot_create_overlapping_leave_request(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        
        LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::SICK,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'reason' => 'Sakit',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $response = $this->actingAs($employee->user)->post(route('pegawai.leave-requests.store'), [
            'type' => LeaveRequestType::LEAVE->value,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'reason' => 'Cuti',
        ]);

        $response->assertSessionHasErrors('start_date');
        $this->assertDatabaseCount('leave_requests', 1);
    }

    public function test_employee_can_cancel_pending_leave_request(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        
        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::SICK,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'reason' => 'Sakit',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $response = $this->actingAs($employee->user)->patch(route('pegawai.leave-requests.cancel', $request));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $request->id,
            'status' => LeaveRequestStatus::CANCELLED->value,
        ]);
    }

    public function test_admin_can_approve_leave_request_and_sync_attendance(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        
        WorkSchedule::create([
            'name' => 'Senin', 'day_of_week' => now()->addDays(1)->dayOfWeek,
            'check_in_start' => '07:00:00', 'check_in_time' => '08:00:00', 'check_in_end' => '10:00:00',
            'late_tolerance_minutes' => 15, 'check_out_start' => '16:00:00',
            'check_out_time' => '17:00:00', 'check_out_end' => '19:00:00',
            'is_workday' => true, 'is_default' => true, 'is_active' => true,
        ]);

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::OFFICIAL_DUTY,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'reason' => 'Dinas',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $response = $this->actingAs($admin->user)->patch(route('admin.leave-requests.approve', $request), [
            'approval_note' => 'Disetujui untuk dinas',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('leave_requests', [
            'id' => $request->id,
            'status' => LeaveRequestStatus::APPROVED->value,
            'approved_by' => $admin->user->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'attendance_status' => AttendanceStatus::OFFICIAL_DUTY->value,
            'leave_request_id' => $request->id,
        ]);
    }

    public function test_admin_can_reject_leave_request_with_note(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        
        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::PERMISSION,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'reason' => 'Izin',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $response = $this->actingAs($admin->user)->patch(route('admin.leave-requests.reject', $request), [
            'approval_note' => 'Tidak diizinkan, kurang personil',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $request->id,
            'status' => LeaveRequestStatus::REJECTED->value,
        ]);
        
        $this->assertDatabaseCount('attendances', 0);
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
