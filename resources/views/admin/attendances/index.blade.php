@extends('layouts.admin')

@section('page-title', 'Log Absensi')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-xl font-bold text-neglasari-dark">Log Absensi</h2>
            <div class="flex items-center space-x-2 mt-4 md:mt-0">
                <a href="{{ route('admin.attendances.export') }}" class="px-4 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150 text-sm">
                    Ekspor Excel
                </a>
            </div>
        </div>
        
        <form method="GET" action="{{ route('admin.attendances.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="employee_id" class="block text-sm font-semibold text-neglasari-text mb-1">Pegawai</label>
                    <select name="employee_id" id="employee_id" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Semua Pegawai</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->position->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-neglasari-text mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Semua Status</option>
                        <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                        <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                        <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                        <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="leave" {{ request('status') === 'leave' ? 'selected' : '' }}>Cuti</option>
                        <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Alpa</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                        Filter
                    </button>
                </div>
            </div>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neglasari-border">
                <thead class="bg-neglasari-bg">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Pulang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neglasari-border">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-neglasari-bg transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-neglasari-text">
                                <div class="font-semibold">{{ $attendance->employee->full_name }}</div>
                                <div class="text-xs text-neglasari-text-secondary">{{ $attendance->employee->position->name }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-neglasari-text">
                                {{ $attendance->attendance_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                @if($attendance->check_in_at)
                                    <div class="font-semibold {{ $attendance->late_minutes > 0 ? 'text-yellow-600' : 'text-neglasari-text' }}">
                                        {{ $attendance->check_in_at->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-neglasari-text-secondary">
                                        {{ ($attendance->check_in_location_status?->value ?? $attendance->check_in_location_status) === 'inside_radius' ? 'Dalam Radius' : 'Luar Radius' }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                @if($attendance->check_out_at)
                                    <div class="font-semibold {{ $attendance->early_leave_minutes > 0 ? 'text-yellow-600' : 'text-neglasari-text' }}">
                                        {{ $attendance->check_out_at->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-neglasari-text-secondary">
                                        {{ ($attendance->check_out_status?->value ?? $attendance->check_out_status) === 'normal' ? 'Normal' : (($attendance->check_out_status?->value ?? $attendance->check_out_status) === 'early_leave' ? 'Pulang Awal' : 'Lembur') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                @php
                                    $st = $attendance->attendance_status?->value ?? $attendance->attendance_status;
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                                    $st === 'present' ? 'bg-green-100 text-green-800' :
                                    ($st === 'late' ? 'bg-yellow-100 text-yellow-800' :
                                    ($st === 'absent' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))
                                }}">
                                    {{ is_object($attendance->attendance_status) ? $attendance->attendance_status->label() : $attendance->attendance_status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-neglasari-text">
                                @if($attendance->work_duration_minutes > 0)
                                    {{ floor($attendance->work_duration_minutes / 60) }} jam {{ $attendance->work_duration_minutes % 60 }} menit
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.attendances.show', $attendance) }}" class="text-neglasari-accent hover:text-neglasari-main">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-neglasari-text-secondary">
                                Tidak ada data absensi yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
