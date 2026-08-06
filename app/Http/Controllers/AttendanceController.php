<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\CheckOutStatus;
use App\Enums\LocationStatus;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly Request $request) {}

    public function query()
    {
        return Attendance::query()->with(['employee.position', 'workSchedule', 'officeLocation'])
            ->when($this->request->filled('employee_id'), fn ($query) => $query->where('employee_id', $this->request->integer('employee_id')))
            ->when($this->request->filled('date'), fn ($query) => $query->whereDate('attendance_date', $this->request->date))
            ->when($this->request->filled('status'), fn ($query) => $query->where('attendance_status', $this->request->status))
            ->latest('attendance_date')->latest('check_in_at');
    }

    public function headings(): array
    {
        return ['Nomor Pegawai', 'Nama Pegawai', 'Jabatan', 'Tanggal', 'Masuk', 'Pulang', 'Status Absensi', 'Durasi Kerja', 'Terlambat (menit)', 'Pulang Awal (menit)'];
    }

    public function map($attendance): array
    {
        return [
            $attendance->employee->employee_number,
            $attendance->employee->full_name,
            $attendance->employee->position->name,
            $attendance->attendance_date->format('d/m/Y'),
            $attendance->check_in_at?->format('H:i:s') ?? '-',
            $attendance->check_out_at?->format('H:i:s') ?? '-',
            $attendance->attendance_status->label(),
            sprintf('%d jam %d menit', intdiv($attendance->work_duration_minutes, 60), $attendance->work_duration_minutes % 60),
            $attendance->late_minutes,
            $attendance->early_leave_minutes,
        ];
    }
}

class AttendanceController extends Controller
{
    public function attendancePage()
    {
        $employee = $this->employee();
        $attendance = Attendance::whereBelongsTo($employee)->whereDate('attendance_date', today())->first();
        $monthlyAttendances = Attendance::whereBelongsTo($employee)
            ->whereMonth('attendance_date', now()->month)->whereYear('attendance_date', now()->year)
            ->latest('attendance_date')->get();

        return view('employee.attendance', [
            'attendance' => $attendance,
            'monthlyAttendances' => $monthlyAttendances,
            'officeLocation' => $this->officeLocation(),
        ]);
    }

    public function checkinPage()
    {
        $employee = $this->employee();
        if (Attendance::whereBelongsTo($employee)->whereDate('attendance_date', today())->exists()) {
            return redirect()->route('pegawai.attendance.page')->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        return view('employee.checkin', ['officeLocation' => $this->officeLocation()]);
    }

    public function checkin(Request $request)
    {
        $data = $this->validatedEvidence($request);
        $employee = $this->employee();
        $office = $this->officeLocation();
        $schedule = $this->schedule($employee);
        $distance = $this->distance($data['latitude'], $data['longitude'], $office->latitude, $office->longitude);
        $this->validateLocation($data, $office, $distance);

        $now = now();
        $lateAt = Carbon::parse($now->toDateString().' '.$schedule->check_in_time)->addMinutes($schedule->late_tolerance_minutes);
        $lateMinutes = $now->greaterThan($lateAt) ? (int) $lateAt->diffInMinutes($now) : 0;

        DB::transaction(function () use ($request, $data, $employee, $office, $schedule, $distance, $now, $lateMinutes) {
            if (Attendance::whereBelongsTo($employee)->whereDate('attendance_date', today())->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['attendance' => 'Anda sudah melakukan absen masuk hari ini.']);
            }

            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'attendance_date' => $now->toDateString(),
                'work_schedule_id' => $schedule->id,
                'office_location_id' => $office->id,
                'check_in_at' => $now,
                'check_in_latitude' => $data['latitude'],
                'check_in_longitude' => $data['longitude'],
                'check_in_accuracy' => $data['accuracy'],
                'check_in_distance' => round($distance, 2),
                'check_in_photo_path' => $request->file('photo')->store('attendance/check-in', config('filesystems.default')),
                'attendance_status' => $lateMinutes > 0 ? AttendanceStatus::LATE : AttendanceStatus::PRESENT,
                'check_in_location_status' => $distance <= $office->radius_meters ? LocationStatus::INSIDE_RADIUS : LocationStatus::OUTSIDE_RADIUS,
                'late_minutes' => $lateMinutes,
                'check_in_ip' => $request->ip(),
                'check_in_user_agent' => $request->userAgent(),
            ]);
            $this->log('check_in', 'Absen masuk: '.$employee->full_name, $attendance, $request);
        });

        return redirect()->route('pegawai.attendance.page')->with('success', 'Absen masuk berhasil dicatat.');
    }

    public function checkoutPage()
    {
        $attendance = $this->todayAttendance();
        if ($attendance->check_out_at) {
            return redirect()->route('pegawai.attendance.page')->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        return view('employee.checkout', ['attendance' => $attendance, 'officeLocation' => $this->officeLocation()]);
    }

    public function checkout(Request $request)
    {
        $data = $this->validatedEvidence($request);
        $attendance = $this->todayAttendance();
        $office = $this->officeLocation();
        $distance = $this->distance($data['latitude'], $data['longitude'], $office->latitude, $office->longitude);
        $this->validateLocation($data, $office, $distance);
        $now = now();
        $scheduledCheckout = Carbon::parse($now->toDateString().' '.$attendance->workSchedule->check_out_time);
        $earlyMinutes = $now->lessThan($scheduledCheckout) ? (int) $now->diffInMinutes($scheduledCheckout) : 0;

        DB::transaction(function () use ($request, $data, $attendance, $office, $distance, $now, $earlyMinutes, $scheduledCheckout) {
            $attendance->refresh();
            if ($attendance->check_out_at) {
                throw ValidationException::withMessages(['attendance' => 'Anda sudah melakukan absen pulang hari ini.']);
            }
            $attendance->update([
                'check_out_at' => $now,
                'check_out_latitude' => $data['latitude'],
                'check_out_longitude' => $data['longitude'],
                'check_out_accuracy' => $data['accuracy'],
                'check_out_distance' => round($distance, 2),
                'check_out_photo_path' => $request->file('photo')->store('attendance/check-out', config('filesystems.default')),
                'check_out_location_status' => $distance <= $office->radius_meters ? LocationStatus::INSIDE_RADIUS : LocationStatus::OUTSIDE_RADIUS,
                'check_out_status' => $earlyMinutes > 0 ? CheckOutStatus::EARLY_LEAVE : ($now->greaterThan($scheduledCheckout) ? CheckOutStatus::OVERTIME : CheckOutStatus::NORMAL),
                'early_leave_minutes' => $earlyMinutes,
                'work_duration_minutes' => max(0, (int) $attendance->check_in_at->diffInMinutes($now)),
                'check_out_ip' => $request->ip(),
                'check_out_user_agent' => $request->userAgent(),
            ]);
            $this->log('check_out', 'Absen pulang: '.$attendance->employee->full_name, $attendance, $request);
        });

        return redirect()->route('pegawai.attendance.page')->with('success', 'Absen pulang berhasil dicatat.');
    }

    public function index(Request $request)
    {
        $attendances = Attendance::with(['employee.position', 'workSchedule'])
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->integer('employee_id')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('attendance_date', $request->date))
            ->when($request->filled('status'), fn ($query) => $query->where('attendance_status', $request->status))
            ->latest('attendance_date')->latest('check_in_at')->paginate(20);
        $employees = Employee::active()->orderBy('full_name')->get();

        return view('admin.attendances.index', compact('attendances', 'employees'));
    }

    public function export(Request $request)
    {
        return (new AttendanceExport($request))->download('log_absensi_'.now()->format('Ymd_His').'.xlsx', Excel::XLSX);
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['employee.position', 'workSchedule', 'officeLocation']);
        return view('admin.attendances.show', ['attendance' => $attendance, 'officeLocation' => $attendance->officeLocation ?? $this->officeLocation()]);
    }

    public function manualCheckout(Attendance $attendance)
    {
        abort_if($attendance->check_out_at, 422, 'Pegawai sudah melakukan absen pulang.');
        $office = $this->officeLocation();
        $attendance->update([
            'check_out_at' => now(), 'check_out_latitude' => $office->latitude, 'check_out_longitude' => $office->longitude,
            'check_out_accuracy' => 0, 'check_out_distance' => 0, 'check_out_location_status' => LocationStatus::INSIDE_RADIUS,
            'check_out_status' => CheckOutStatus::NORMAL, 'work_duration_minutes' => max(0, (int) $attendance->check_in_at->diffInMinutes(now())),
            'check_out_ip' => request()->ip(), 'check_out_user_agent' => request()->userAgent(),
        ]);
        $this->log('manual_checkout', 'Absen pulang manual: '.$attendance->employee->full_name, $attendance, request());

        return back()->with('success', 'Absen pulang manual berhasil.');
    }

    private function validatedEvidence(Request $request): array
    {
        return $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'min:0', 'max:10000'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function validateLocation(array $data, OfficeLocation $office, float $distance): void
    {
        $errors = [];
        if ($data['accuracy'] > $office->maximum_accuracy_meters) $errors['accuracy'] = 'Akurasi GPS terlalu rendah. Coba di area terbuka.';
        if ($distance > $office->radius_meters && ! $office->allow_outside_radius) $errors['latitude'] = 'Anda berada di luar radius absensi yang diizinkan.';
        if ($errors) throw ValidationException::withMessages($errors);
    }

    private function employee(): Employee
    {
        return Auth::user()->employee ?? abort(403, 'Akun tidak terhubung dengan data pegawai.');
    }

    private function officeLocation(): OfficeLocation
    {
        return OfficeLocation::where('is_active', true)->first() ?? abort(503, 'Lokasi kantor belum dikonfigurasi.');
    }

    private function schedule(Employee $employee): WorkSchedule
    {
        $day = now()->dayOfWeek;
        return $employee->workSchedules()->where('day_of_week', $day)->where('is_active', true)->first()
            ?? WorkSchedule::where('day_of_week', $day)->where('is_default', true)->where('is_active', true)->first()
            ?? abort(422, 'Jadwal kerja hari ini belum dikonfigurasi.');
    }

    private function todayAttendance(): Attendance
    {
        return Attendance::with(['employee', 'workSchedule'])->whereBelongsTo($this->employee())->whereDate('attendance_date', today())->first()
            ?? abort(422, 'Anda belum melakukan absen masuk hari ini.');
    }

    private function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radius = 6371000;
        $phi1 = deg2rad($lat1); $phi2 = deg2rad($lat2);
        $deltaPhi = deg2rad($lat2 - $lat1); $deltaLambda = deg2rad($lon2 - $lon1);
        $a = sin($deltaPhi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($deltaLambda / 2) ** 2;
        return $radius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function log(string $action, string $description, Attendance $attendance, Request $request): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(), 'action' => $action, 'module' => 'attendance', 'description' => $description,
            'subject_type' => Attendance::class, 'subject_id' => $attendance->id,
            'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(),
        ]);
    }
}
