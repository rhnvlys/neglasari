@extends('layouts.admin')

@section('page-title', 'Detail Pegawai')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-neglasari-dark">Detail Pegawai</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.employees.edit', $employee) }}" class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-xl shadow-md hover:bg-yellow-600 transition duration-150 text-sm">
                    Edit
                </a>
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150 text-sm">
                    Kembali
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="md:col-span-1 flex flex-col items-center">
                <div class="w-32 h-32 rounded-full bg-neglasari-main flex items-center justify-center text-white shadow-lg mb-4">
                    <span class="font-bold text-4xl">{{ substr($employee->full_name, 0, 1) }}</span>
                </div>
                <h3 class="text-xl font-bold text-neglasari-dark text-center">{{ $employee->full_name }}</h3>
                <p class="text-sm text-neglasari-text-secondary">{{ $employee->position->name }}</p>
                <span class="mt-2 px-3 py-1 text-xs font-semibold rounded-full {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            
            <div class="md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-neglasari-bg rounded-xl p-4">
                        <h4 class="text-lg font-bold text-neglasari-dark mb-3">Informasi Pribadi</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Nomor Pegawai</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->employee_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">NIK</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->nik ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Jenis Kelamin</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->gender?->label() ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Tempat, Tanggal Lahir</span>
                                <span class="text-sm font-semibold text-neglasari-text text-right">
                                    {{ $employee->birth_place ?? '-' }}, {{ optional($employee->birth_date)->format('d M Y') ?? '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Alamat</span>
                                <span class="text-sm font-semibold text-neglasari-text text-right">{{ $employee->address ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-neglasari-bg rounded-xl p-4">
                        <h4 class="text-lg font-bold text-neglasari-dark mb-3">Informasi Kontak & Kerja</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Nomor Telepon</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->phone ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Email</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->email ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Jabatan</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->position->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Status Kepegawaian</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ $employee->employment_status?->label() ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-neglasari-text-secondary">Tanggal Bergabung</span>
                                <span class="text-sm font-semibold text-neglasari-text">{{ optional($employee->joined_at)->format('d M Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-neglasari-bg rounded-xl p-4">
                <h4 class="text-lg font-bold text-neglasari-dark mb-3">Riwayat Absensi Terbaru</h4>
                <div class="space-y-3">
                    @forelse($employee->attendances->take(5) as $attendance)
                        <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                            <div>
                                <p class="text-sm font-semibold text-neglasari-text">{{ $attendance->attendance_date->format('d M Y') }}</p>
                                <p class="text-xs text-neglasari-text-secondary">{{ $attendance->attendance_status->label() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-neglasari-text">{{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i') : '-' }}</p>
                                <p class="text-xs text-neglasari-text">{{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '-' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neglasari-text-secondary text-center py-4">Belum ada riwayat absensi.</p>
                    @endforelse
                </div>
            </div>
            
            <div class="bg-neglasari-bg rounded-xl p-4">
                <h4 class="text-lg font-bold text-neglasari-dark mb-3">Riwayat Izin/Cuti Terbaru</h4>
                <div class="space-y-3">
                    @forelse($employee->leaveRequests->take(5) as $leave)
                        <div class="flex items-center justify-between p-3 bg-white rounded-xl">
                            <div>
                                <p class="text-sm font-semibold text-neglasari-text">{{ $leave->type->label() }}</p>
                                <p class="text-xs text-neglasari-text-secondary">{{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                                $leave->status === 'approved' ? 'bg-green-100 text-green-800' :
                                ($leave->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                            }}">
                                {{ $leave->status->label() }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-neglasari-text-secondary text-center py-4">Belum ada riwayat izin/cuti.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
