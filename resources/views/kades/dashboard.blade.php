@extends('layouts.kades')

@section('page-title', 'Dashboard Kepala Desa')

@section('kades-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-xl font-bold text-neglasari-dark mb-4">Statistik Absensi Bulan Ini</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-neglasari-bg rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-neglasari-main">{{ $monthlyStats['present'] }}</p>
                <p class="text-sm text-neglasari-text-secondary">Total Hadir</p>
            </div>
            <div class="bg-neglasari-bg rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-yellow-500">{{ $monthlyStats['late'] }}</p>
                <p class="text-sm text-neglasari-text-secondary">Terlambat</p>
            </div>
            <div class="bg-neglasari-bg rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-red-500">{{ $monthlyStats['absent'] }}</p>
                <p class="text-sm text-neglasari-text-secondary">Alpa</p>
            </div>
            <div class="bg-neglasari-bg rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-blue-500">{{ $monthlyStats['leave'] }}</p>
                <p class="text-sm text-neglasari-text-secondary">Izin/Sakit</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-neglasari-dark">Pengajuan Izin Pending</h2>
                <a href="{{ route('kades.leave-requests.index') }}" class="text-xs font-semibold text-neglasari-main hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-4">
                @forelse($pendingLeaves as $leave)
                    <div class="flex items-center justify-between p-3 bg-neglasari-bg rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold">
                                {{ substr($leave->employee->full_name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-neglasari-text">{{ $leave->employee->full_name }}</p>
                                <p class="text-xs text-neglasari-text-secondary">{{ $leave->type->label() }} - {{ $leave->start_date->format('d M') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('kades.leave-requests.show', $leave) }}" class="px-3 py-1 bg-white text-neglasari-main hover:bg-gray-100 text-xs font-bold rounded-lg border border-gray-200">Proses</a>
                    </div>
                @empty
                    <p class="text-sm text-neglasari-text-secondary">Tidak ada pengajuan izin/cuti yang menunggu.</p>
                @endforelse
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
            <h2 class="text-xl font-bold text-neglasari-dark mb-4">Log Aktivitas Terbaru</h2>
            <div class="space-y-4">
                @forelse($recentActivities as $log)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-neglasari-main flex items-center justify-center text-white text-xs font-semibold">
                            {{ substr($log->user->name, 0, 2) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-neglasari-text">{{ $log->user->name }}</p>
                            <p class="text-xs text-neglasari-text-secondary">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="border-neglasari-border">
                    @endif
                @empty
                    <p class="text-sm text-neglasari-text-secondary">Tidak ada aktivitas terbaru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
