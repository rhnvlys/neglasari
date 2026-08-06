@extends('layouts.employee')

@section('title', '- Dashboard')

@section('employee-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-neglasari-dark">Selamat Datang, {{ Auth::user()->employee->full_name ?? Auth::user()->name }}</h2>
                <p class="text-sm text-neglasari-text-secondary">Status: <span class="font-semibold text-neglasari-main">Aktif</span></p>
            </div>
            <div class="w-16 h-16 rounded-full bg-neglasari-main flex items-center justify-center text-white shadow-lg">
                <span class="font-bold text-xl">{{ substr(Auth::user()->employee->full_name ?? Auth::user()->name, 0, 1) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-lg font-bold text-neglasari-dark mb-4">Absensi Hari Ini</h2>
        <div class="flex justify-center">
            <div class="w-32 h-32 rounded-full bg-neglasari-bg flex flex-col items-center justify-center border-4 border-neglasari-border">
                <p class="text-xs text-neglasari-text-secondary">Status</p>
                <p class="text-2xl font-bold text-red-500 mt-1">Belum Absen</p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('H:i') }}</p>
            </div>
        </div>
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('pegawai.attendance.checkin') }}" class="px-6 py-3 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                Absen Masuk
            </a>
            <a href="{{ route('pegawai.attendance.checkout') }}" class="px-6 py-3 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150 opacity-50 cursor-not-allowed">
                Absen Pulang
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-lg font-bold text-neglasari-dark mb-4">Statistik Bulan Ini</h2>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-neglasari-main">{{ $monthlyStats['present'] ?? 0 }}</p>
                <p class="text-xs text-neglasari-text-secondary">Hadir</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500">{{ $monthlyStats['late'] ?? 0 }}</p>
                <p class="text-xs text-neglasari-text-secondary">Terlambat</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-blue-500">{{ $monthlyStats['leave'] ?? 0 }}</p>
                <p class="text-xs text-neglasari-text-secondary">Izin</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-neglasari-dark">Pengajuan Terbaru</h2>
            <a href="{{ route('pegawai.leave-requests.create') }}" class="text-xs text-white bg-neglasari-main px-3 py-1.5 rounded-lg hover:bg-neglasari-accent transition">Buat Baru</a>
        </div>
        
        <div class="space-y-3">
            @forelse($pendingLeaves ?? [] as $leave)
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-neglasari-dark text-sm">{{ $leave->type->label() }}</p>
                        <p class="text-xs text-gray-500">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}</p>
                    </div>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-[10px] font-bold rounded-full uppercase">Menunggu</span>
                </div>
            @empty
                <div class="text-center py-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-sm text-gray-500">Tidak ada pengajuan yang sedang menunggu persetujuan.</p>
                </div>
            @endforelse
        </div>
        @if(count($pendingLeaves ?? []) > 0)
            <div class="mt-4 text-center">
                <a href="{{ route('pegawai.leave-requests.index') }}" class="text-sm text-neglasari-main font-semibold hover:underline">Lihat Semua Pengajuan</a>
            </div>
        @endif
    </div>
</div>
</div>
@endsection
