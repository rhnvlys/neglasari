@extends('layouts.admin')

@section('title', 'Detail Pengajuan - SIAP Neglasari')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-neglasari-dark">Detail Pengajuan Pegawai</h1>
        <a href="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.index' : 'admin.leave-requests.index') }}" class="text-sm text-neglasari-main font-semibold hover:underline">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-neglasari-border overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 p-6 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-neglasari-main text-white rounded-full flex items-center justify-center font-bold text-lg">
                    {{ substr($leaveRequest->employee->full_name, 0, 1) }}
                </div>
                <div>
                    <h2 class="font-bold text-neglasari-dark text-lg">{{ $leaveRequest->employee->full_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $leaveRequest->employee->position->name }} &bull; {{ $leaveRequest->employee->employee_number }}</p>
                </div>
            </div>
            <div>
                <span class="inline-block px-4 py-1.5 font-bold rounded-full text-sm
                    {{ $leaveRequest->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $leaveRequest->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $leaveRequest->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $leaveRequest->status->value === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                ">
                    {{ $leaveRequest->status->label() }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Jenis Pengajuan</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->type->label() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Waktu Pengajuan</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Mulai</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->start_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Selesai</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->end_date->format('d M Y') }}</p>
                </div>
            </div>

            <hr class="border-gray-100">

            <div>
                <p class="text-sm text-gray-500 font-medium mb-2">Alasan Lengkap</p>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-gray-800 whitespace-pre-line">{{ $leaveRequest->reason }}</p>
                </div>
            </div>

            @if($leaveRequest->attachment_path)
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-2">Lampiran</p>
                    <a href="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.attachment' : 'admin.leave-requests.attachment', $leaveRequest) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 text-neglasari-text rounded-lg text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2 text-neglasari-main">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Lihat Dokumen Terlampir
                    </a>
                </div>
            @endif

            <!-- Processing Section -->
            @if($leaveRequest->status->value === 'pending')
                @can('approve', $leaveRequest)
                    <hr class="border-gray-100">
                    <div>
                        <h3 class="text-lg font-bold text-neglasari-dark mb-4">Proses Pengajuan</h3>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
                            <strong>Perhatian:</strong> Menyetujui pengajuan ini akan otomatis mengisi absensi administratif pegawai pada rentang tanggal tersebut, kecuali untuk hari libur.
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Form Setuju -->
                            <form action="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.approve' : 'admin.leave-requests.approve', $leaveRequest) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui pengajuan ini?');" class="bg-white border border-green-200 rounded-xl p-4">
                                @csrf
                                @method('PATCH')
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Persetujuan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <textarea name="approval_note" rows="2" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 text-sm mb-3" placeholder="Misal: Harap koordinasi tugas sebelum cuti..."></textarea>
                                <button type="submit" class="w-full py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition">
                                    Setujui Pengajuan
                                </button>
                            </form>

                            <!-- Form Tolak -->
                            <form action="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.reject' : 'admin.leave-requests.reject', $leaveRequest) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menolak pengajuan ini?');" class="bg-white border border-red-200 rounded-xl p-4">
                                @csrf
                                @method('PATCH')
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="approval_note" rows="2" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50 text-sm mb-3" placeholder="Alasan penolakan..." required></textarea>
                                @error('approval_note') <p class="text-red-500 text-xs mb-2 -mt-2">{{ $message }}</p> @enderror
                                <button type="submit" class="w-full py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">
                                    Tolak Pengajuan
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-gray-50 p-4 rounded-xl text-center text-gray-500 text-sm">
                        Anda tidak memiliki wewenang untuk memproses pengajuan ini.
                    </div>
                @endcan
            @else
                <hr class="border-gray-100">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Informasi Pemrosesan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 font-medium mb-1">Diproses Oleh</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $leaveRequest->approver?->name ?? 'Sistem' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium mb-1">Waktu Pemrosesan</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $leaveRequest->approved_at?->format('d M Y H:i') }}</p>
                        </div>
                        @if($leaveRequest->approval_note)
                            <div class="col-span-1 md:col-span-2 mt-2">
                                <p class="text-xs text-gray-500 font-medium mb-1">Catatan Persetujuan/Penolakan</p>
                                <div class="text-sm text-gray-800 bg-white p-3 rounded-lg border border-gray-200">
                                    {{ $leaveRequest->approval_note }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
