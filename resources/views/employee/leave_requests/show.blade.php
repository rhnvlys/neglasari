@extends('layouts.employee')

@section('title', '- Detail Pengajuan')

@section('employee-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-neglasari-dark">Detail Pengajuan</h2>
            <a href="{{ route('pegawai.leave-requests.index') }}" class="text-sm text-neglasari-main font-semibold hover:underline">Kembali</a>
        </div>

        <div class="mb-6 flex justify-between items-center bg-neglasari-bg p-4 rounded-xl">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Status</p>
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                    {{ $leaveRequest->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $leaveRequest->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $leaveRequest->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $leaveRequest->status->value === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                ">
                    {{ $leaveRequest->status->label() }}
                </span>
            </div>
            @if($leaveRequest->status->value === 'pending')
                <form action="{{ route('pegawai.leave-requests.cancel', $leaveRequest) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 font-semibold rounded-xl text-sm transition">
                        Batalkan Pengajuan
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Jenis Pengajuan</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->type->label() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tanggal Pengajuan</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Mulai</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->start_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Selesai</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->end_date->format('d M Y') }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500 font-medium">Durasi Kalender</p>
                    <p class="font-semibold text-neglasari-dark">{{ $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }} hari</p>
                </div>
            </div>

            <hr class="border-gray-100">

            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Alasan</p>
                <p class="text-gray-800 whitespace-pre-line">{{ $leaveRequest->reason }}</p>
            </div>

            @if($leaveRequest->attachment_path)
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-2">Lampiran</p>
                    <a href="{{ route('pegawai.leave-requests.attachment', $leaveRequest) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-neglasari-text rounded-lg text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Lihat / Unduh Lampiran
                    </a>
                </div>
            @endif

            @if($leaveRequest->status->value === 'approved' || $leaveRequest->status->value === 'rejected')
                <hr class="border-gray-100">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Informasi Pemrosesan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Diproses Oleh</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $leaveRequest->approver?->name ?? 'Sistem' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Waktu Pemrosesan</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $leaveRequest->approved_at?->format('d M Y H:i') }}</p>
                        </div>
                        @if($leaveRequest->approval_note)
                            <div class="col-span-1 md:col-span-2">
                                <p class="text-xs text-gray-500 font-medium">Catatan Persetujuan/Penolakan</p>
                                <p class="text-sm text-gray-800 bg-white p-3 rounded-lg border border-gray-200 mt-1">{{ $leaveRequest->approval_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
