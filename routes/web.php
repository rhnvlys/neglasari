<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OfficeLocationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WorkScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole(['Admin', 'Super Admin', 'Admin Desa'])) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('anggota.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/health', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        \Illuminate\Support\Facades\Cache::set('health_check', true, 10);
        return response()->json([
            'status' => 'ok',
            'database' => 'connected',
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Service Unavailable',
        ], 503);
    }
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Group Route Role Admin (Staff IT Desa / Administrator)
Route::middleware(['auth', 'active.user', 'role:Admin|Super Admin|Admin Desa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('schedules', WorkScheduleController::class);
    Route::resource('locations', OfficeLocationController::class);
    Route::resource('holidays', HolidayController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/audit-logs', [ActivityLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/export', [AttendanceController::class, 'export'])->name('attendances.export');
    Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::post('/attendances/{attendance}/manual-checkout', [AttendanceController::class, 'manualCheckout'])->name('attendances.manual-checkout');

    // 11 Laporan Absensi Admin
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'monthly'])->name('monthly');
        Route::get('/yearly', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'yearly'])->name('yearly');
        Route::get('/employee', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'employee'])->name('employee');
        Route::get('/position', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'position'])->name('position');
        Route::get('/late', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'late'])->name('late');
        Route::get('/leave-requests', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'leaveRequests'])->name('leave-requests');
        Route::get('/missing-checkout', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'missingCheckout'])->name('missing-checkout');
        Route::get('/summary', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'summary'])->name('summary');
        Route::get('/administrative', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'administrative'])->name('administrative');

        Route::get('/export', [\App\Http\Controllers\Reports\AttendanceExportController::class, 'export'])->name('export');
    });

    // Pengajuan Izin/Cuti
    Route::get('/pengajuan', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::patch('/pengajuan/{leaveRequest}/setujui', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/pengajuan/{leaveRequest}/tolak', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::get('/pengajuan/{leaveRequest}/lampiran', [LeaveRequestController::class, 'attachment'])->name('leave-requests.attachment');
});

// Group Route Role Anggota (Perangkat & Staf)
Route::middleware(['auth', 'active.user', 'role:Anggota|Pegawai|Kepala Desa'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'employee'])->name('dashboard');
    Route::get('/absensi', [AttendanceController::class, 'attendancePage'])->name('attendance.page');
    Route::get('/absensi/masuk', [AttendanceController::class, 'checkinPage'])->name('attendance.checkin');
    Route::post('/absensi/masuk', [AttendanceController::class, 'checkin'])->name('attendance.checkin.store');
    Route::get('/absensi/pulang', [AttendanceController::class, 'checkoutPage'])->name('attendance.checkout');
    Route::post('/absensi/pulang', [AttendanceController::class, 'checkout'])->name('attendance.checkout.store');

    // Riwayat Laporan Pribadi Anggota
    Route::get('/riwayat-laporan', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'history'])->name('reports.history');
    Route::get('/riwayat-laporan/export', [\App\Http\Controllers\Reports\AttendanceExportController::class, 'export'])->name('reports.export');

    // Pengajuan Izin/Cuti Anggota
    Route::get('/pengajuan', [LeaveRequestController::class, 'myRequests'])->name('leave-requests.index');
    Route::get('/pengajuan/tambah', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/pengajuan', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'showMine'])->name('leave-requests.show');
    Route::patch('/pengajuan/{leaveRequest}/batalkan', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    Route::get('/pengajuan/{leaveRequest}/lampiran', [LeaveRequestController::class, 'attachment'])->name('leave-requests.attachment');
});

// Compatibility Route Group for /pegawai/* & /kades/*
Route::middleware(['auth', 'active.user', 'role:Anggota|Pegawai|Kepala Desa'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'employee'])->name('dashboard');
    Route::get('/absensi', [AttendanceController::class, 'attendancePage'])->name('attendance.page');
    Route::get('/absensi/masuk', [AttendanceController::class, 'checkinPage'])->name('attendance.checkin');
    Route::post('/absensi/masuk', [AttendanceController::class, 'checkin'])->name('attendance.checkin.store');
    Route::get('/absensi/pulang', [AttendanceController::class, 'checkoutPage'])->name('attendance.checkout');
    Route::post('/absensi/pulang', [AttendanceController::class, 'checkout'])->name('attendance.checkout.store');

    Route::get('/riwayat-laporan', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'history'])->name('reports.history');
    Route::get('/riwayat-laporan/export', [\App\Http\Controllers\Reports\AttendanceExportController::class, 'export'])->name('reports.export');

    Route::get('/pengajuan', [LeaveRequestController::class, 'myRequests'])->name('leave-requests.index');
    Route::get('/pengajuan/tambah', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/pengajuan', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'showMine'])->name('leave-requests.show');
    Route::patch('/pengajuan/{leaveRequest}/batalkan', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    Route::get('/pengajuan/{leaveRequest}/lampiran', [LeaveRequestController::class, 'attachment'])->name('leave-requests.attachment');
});

Route::middleware(['auth', 'active.user', 'role:Admin|Super Admin|Admin Desa|Kepala Desa'])->prefix('kades')->name('kades.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kades'])->name('dashboard');
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'monthly'])->name('monthly');
        Route::get('/summary', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'summary'])->name('summary');
        Route::get('/export', [\App\Http\Controllers\Reports\AttendanceExportController::class, 'export'])->name('export');
    });

    Route::get('/pengajuan', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::patch('/pengajuan/{leaveRequest}/setujui', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/pengajuan/{leaveRequest}/tolak', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::get('/pengajuan/{leaveRequest}/lampiran', [LeaveRequestController::class, 'attachment'])->name('leave-requests.attachment');
});
