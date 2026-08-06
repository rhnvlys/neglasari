<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkSchedule;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_and_protected_dashboard_requires_authentication(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_employee_can_check_in_with_valid_location_and_selfie(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $employee = $this->employeeUser();
        $office = OfficeLocation::create([
            'name' => 'Kantor Desa', 'address' => 'Neglasari', 'latitude' => -6.9,
            'longitude' => 107.6, 'radius_meters' => 200, 'maximum_accuracy_meters' => 50,
            'requires_photo' => true, 'allow_outside_radius' => false,
            'requires_outside_verification' => true, 'is_active' => true,
        ]);
        WorkSchedule::create([
            'name' => 'Jadwal Hari Ini', 'day_of_week' => now()->dayOfWeek,
            'check_in_start' => '00:00:00', 'check_in_time' => '23:59:00', 'check_in_end' => '23:59:59',
            'late_tolerance_minutes' => 0, 'check_out_start' => '00:00:00',
            'check_out_time' => '23:59:00', 'check_out_end' => '23:59:59',
            'is_workday' => true, 'is_default' => true, 'is_active' => true,
        ]);

        $this->actingAs($employee->user)->post('/pegawai/absensi/masuk', [
            'latitude' => $office->latitude,
            'longitude' => $office->longitude,
            'accuracy' => 10,
            'photo' => UploadedFile::fake()->image('selfie.jpg', 320, 320),
        ])->assertRedirect('/pegawai/absensi');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'office_location_id' => $office->id,
            'check_in_location_status' => 'inside_radius',
        ]);
    }

    public function test_employee_cannot_check_in_outside_allowed_radius(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $employee = $this->employeeUser();
        OfficeLocation::create([
            'name' => 'Kantor Desa', 'address' => 'Neglasari', 'latitude' => -6.9,
            'longitude' => 107.6, 'radius_meters' => 50, 'maximum_accuracy_meters' => 50,
            'requires_photo' => true, 'allow_outside_radius' => false,
            'requires_outside_verification' => true, 'is_active' => true,
        ]);
        WorkSchedule::create([
            'name' => 'Jadwal Hari Ini', 'day_of_week' => now()->dayOfWeek,
            'check_in_start' => '00:00:00', 'check_in_time' => '23:59:00', 'check_in_end' => '23:59:59',
            'late_tolerance_minutes' => 0, 'check_out_start' => '00:00:00',
            'check_out_time' => '23:59:00', 'check_out_end' => '23:59:59',
            'is_workday' => true, 'is_default' => true, 'is_active' => true,
        ]);

        $this->actingAs($employee->user)->from('/pegawai/absensi/masuk')->post('/pegawai/absensi/masuk', [
            'latitude' => -7.5,
            'longitude' => 108.5,
            'accuracy' => 10,
            'photo' => UploadedFile::fake()->image('selfie.jpg', 320, 320),
        ])->assertRedirect('/pegawai/absensi/masuk')->assertSessionHasErrors('latitude');

        $this->assertDatabaseCount('attendances', 0);
    }

    private function employeeUser(): Employee
    {
        $position = Position::create(['name' => 'Kaur', 'is_active' => true]);
        $employee = Employee::create([
            'employee_number' => 'PGW-001', 'full_name' => 'Pegawai Uji', 'gender' => 'male',
            'position_id' => $position->id, 'employment_status' => 'permanent', 'is_active' => true,
        ]);
        $user = User::create([
            'employee_id' => $employee->id, 'name' => $employee->full_name, 'username' => 'pegawai-uji',
            'email' => 'pegawai@example.test', 'password' => 'password123', 'is_active' => true,
        ]);
        $user->assignRole('Pegawai');

        return $employee->setRelation('user', $user);
    }
}
