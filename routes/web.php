<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

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

// Temporary debug route - REMOVE after verification
Route::get('/debug-db', function () {
    try {
        $dbPath = config('database.connections.sqlite.database');
        $users = \Illuminate\Support\Facades\DB::table('users')->select('id', 'username', 'email', 'name')->get();
        $tables = \Illuminate\Support\Facades\DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        return response()->json([
            'db_path' => $dbPath,
            'db_exists' => file_exists($dbPath),
            'db_size' => file_exists($dbPath) ? filesize($dbPath) : 0,
            'source_exists' => file_exists(base_path('database/database.sqlite')),
            'source_size' => file_exists(base_path('database/database.sqlite')) ? filesize(base_path('database/database.sqlite')) : 0,
            'tables' => $tables,
            'user_count' => $users->count(),
            'users' => $users,
        ]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active.user', 'role:Super Admin|Admin Desa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
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

Route::middleware(['auth', 'active.user', 'role:Kepala Desa'])->prefix('kades')->name('kades.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kades'])->name('dashboard');
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');

    // Laporan Absensi Eksekutif
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'monthly'])->name('monthly');
        Route::get('/summary', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'summary'])->name('summary');
        Route::get('/export', [\App\Http\Controllers\Reports\AttendanceExportController::class, 'export'])->name('export');
    });

    // Pengajuan Izin/Cuti
    Route::get('/pengajuan', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::patch('/pengajuan/{leaveRequest}/setujui', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/pengajuan/{leaveRequest}/tolak', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::get('/pengajuan/{leaveRequest}/lampiran', [LeaveRequestController::class, 'attachment'])->name('leave-requests.attachment');
});

Route::middleware(['auth', 'active.user', 'role:Pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'employee'])->name('dashboard');
    Route::get('/absensi', [AttendanceController::class, 'attendancePage'])->name('attendance.page');
    Route::get('/absensi/masuk', [AttendanceController::class, 'checkinPage'])->name('attendance.checkin');
    Route::post('/absensi/masuk', [AttendanceController::class, 'checkin'])->name('attendance.checkin.store');
    Route::get('/absensi/pulang', [AttendanceController::class, 'checkoutPage'])->name('attendance.checkout');
    Route::post('/absensi/pulang', [AttendanceController::class, 'checkout'])->name('attendance.checkout.store');

    // Riwayat Laporan Pribadi Pegawai
    Route::get('/riwayat-laporan', [\App\Http\Controllers\Reports\AttendanceReportController::class, 'history'])->name('reports.history');
    Route::get('/riwayat-laporan/export', [\App\Http\Controllers\Reports\AttendanceExportController::class, 'export'])->name('reports.export');

    // Pengajuan Izin/Cuti
    Route::get('/pengajuan', [LeaveRequestController::class, 'myRequests'])->name('leave-requests.index');
    Route::get('/pengajuan/tambah', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/pengajuan', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/pengajuan/{leaveRequest}', [LeaveRequestController::class, 'showMine'])->name('leave-requests.show');
    Route::patch('/pengajuan/{leaveRequest}/batalkan', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    Route::get('/pengajuan/{leaveRequest}/lampiran', [LeaveRequestController::class, 'attachment'])->name('leave-requests.attachment');
});


