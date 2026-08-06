<?php

namespace Tests\Feature;

use App\Data\Reports\AttendanceReportFilterData;
use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Services\AttendanceReportService;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->service = app(AttendanceReportService::class);
    }

    public function test_calculate_effective_days_and_divide_by_zero_safe(): void
    {
        $employee = $this->createEmployee('2026-08-01');

        $filter = new AttendanceReportFilterData(
            start_date: '2026-08-01',
            end_date: '2026-08-01', // Weekend (Saturday)
        );

        $recap = $this->service->getYearlyRecap($filter);

        $row = $recap->where('employee.id', $employee->id)->first();
        $this->assertNotNull($row);

        // August 1, 2026 is a Saturday. By default, only Mon-Fri are workdays.
        // Effective days = 0.
        $this->assertEquals(0, $row['effective_days']);
        $this->assertEquals(0, $row['physical_present_percent']);
        $this->assertEquals(0, $row['admin_present_percent']);
    }

    public function test_missing_checkout_excludes_administrative_attendances(): void
    {
        $employee = $this->createEmployee('2026-08-01');

        // Physical attendance with missing checkout
        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-03', // Monday
            'check_in_at' => '08:00:00',
            'check_out_at' => null,
            'attendance_status' => AttendanceStatus::PRESENT,
            'is_administrative' => false,
        ]);

        // Administrative attendance with missing checkout (should be ignored)
        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-04',
            'check_in_at' => '08:00:00',
            'check_out_at' => null,
            'attendance_status' => AttendanceStatus::OFFICIAL_DUTY,
            'is_administrative' => true,
        ]);

        $filter = new AttendanceReportFilterData(start_date: '2026-08-01', end_date: '2026-08-31');
        $missing = $this->service->getMissingCheckoutAll($filter);

        $this->assertCount(1, $missing);
        $this->assertEquals('2026-08-03', $missing->first()->attendance_date->toDateString());
    }

    public function test_administrative_report_only_includes_administrative_attendances(): void
    {
        $employee = $this->createEmployee('2026-08-01');

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-03',
            'attendance_status' => AttendanceStatus::PRESENT,
            'is_administrative' => false,
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-04',
            'attendance_status' => AttendanceStatus::OFFICIAL_DUTY,
            'is_administrative' => true,
        ]);

        $filter = new AttendanceReportFilterData(start_date: '2026-08-01', end_date: '2026-08-31');
        $administrative = $this->service->getAdministrativeAll($filter);

        $this->assertCount(1, $administrative);
        $this->assertEquals(AttendanceStatus::OFFICIAL_DUTY, $administrative->first()->attendance_status);
    }

    public function test_late_report_only_includes_late_attendances(): void
    {
        $employee = $this->createEmployee('2026-08-01');

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-03',
            'attendance_status' => AttendanceStatus::PRESENT,
            'is_administrative' => false,
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-08-04',
            'attendance_status' => AttendanceStatus::LATE,
            'late_minutes' => 15,
            'is_administrative' => false,
        ]);

        $filter = new AttendanceReportFilterData(start_date: '2026-08-01', end_date: '2026-08-31');
        $late = $this->service->getLateAll($filter);

        $this->assertCount(1, $late);
        $this->assertEquals(AttendanceStatus::LATE, $late->first()->attendance_status);
        $this->assertEquals(15, $late->first()->late_minutes);
    }

    private function createEmployee(string $joinedAt): Employee
    {
        $position = Position::create(['name' => 'Pos '.uniqid(), 'is_active' => true]);
        return Employee::create([
            'employee_number' => 'EMP-'.uniqid(),
            'full_name' => 'Name '.uniqid(),
            'gender' => 'male',
            'position_id' => $position->id,
            'employment_status' => 'permanent',
            'joined_at' => $joinedAt,
            'is_active' => true,
        ]);
    }
}
