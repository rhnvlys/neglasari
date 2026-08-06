<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkSchedule;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestAttendanceSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_sync_skips_holidays_and_non_working_days(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        
        $startDate = now()->startOfWeek(); // Monday
        $endDate = $startDate->copy()->addDays(6); // Sunday

        // Setup schedule: Monday to Friday working days
        for ($i = 1; $i <= 7; $i++) {
            WorkSchedule::create([
                'name' => 'Day ' . $i, 'day_of_week' => $i,
                'check_in_start' => '07:00:00', 'check_in_time' => '08:00:00', 'check_in_end' => '10:00:00',
                'late_tolerance_minutes' => 15, 'check_out_start' => '16:00:00',
                'check_out_time' => '17:00:00', 'check_out_end' => '19:00:00',
                'is_workday' => $i <= 5, // Sat/Sun not workday
                'is_default' => true, 'is_active' => true,
            ]);
        }

        // Add a holiday on Wednesday
        Holiday::create([
            'name' => 'Libur Nasional',
            'start_date' => $startDate->copy()->addDays(2),
            'end_date' => $startDate->copy()->addDays(2),
            'type' => 'national',
            'applies_to_all' => true,
            'is_active' => true,
        ]);

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::LEAVE,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Cuti Panjang',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $this->actingAs($admin->user)->patch(route('admin.leave-requests.approve', $request), [
            'approval_note' => 'Disetujui',
        ]);

        // Should create attendance for Mon, Tue, Thu, Fri (4 days)
        // Skip Wed (Holiday), Sat, Sun (Non-working)
        $this->assertDatabaseCount('attendances', 4);
        
        $this->assertDatabaseMissing('attendances', [
            'attendance_date' => $startDate->copy()->addDays(2)->toDateString(),
        ]);
    }

    public function test_sync_records_conflict_if_real_check_in_exists(): void
    {
        $employee = $this->createEmployeeWithRole('Pegawai');
        $admin = $this->createEmployeeWithRole('Admin Desa');
        
        $date = now()->startOfWeek();

        WorkSchedule::create([
            'name' => 'Senin', 'day_of_week' => $date->dayOfWeek,
            'check_in_start' => '07:00:00', 'check_in_time' => '08:00:00', 'check_in_end' => '10:00:00',
            'late_tolerance_minutes' => 15, 'check_out_start' => '16:00:00',
            'check_out_time' => '17:00:00', 'check_out_end' => '19:00:00',
            'is_workday' => true, 'is_default' => true, 'is_active' => true,
        ]);

        // Real check in exists
        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $date->toDateString(),
            'work_schedule_id' => 1,
            'attendance_status' => AttendanceStatus::PRESENT,
            'check_in_at' => $date->copy()->setHour(7)->setMinute(30),
        ]);

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => LeaveRequestType::SICK,
            'start_date' => $date,
            'end_date' => $date,
            'reason' => 'Sakit mendadak',
            'status' => LeaveRequestStatus::PENDING,
        ]);

        $this->actingAs($admin->user)->patch(route('admin.leave-requests.approve', $request), [
            'approval_note' => 'OK',
        ]);

        // Should not overwrite real check in
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'attendance_date' => $date->toDateString() . ' 00:00:00',
            'attendance_status' => AttendanceStatus::PRESENT->value, // Still present
        ]);

        // Should record conflict in audit log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'conflict',
            'subject_type' => LeaveRequest::class,
            'subject_id' => $request->id,
        ]);
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
