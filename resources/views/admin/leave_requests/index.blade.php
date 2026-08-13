@extends('layouts.admin')

@section('title', 'Daftar Pengajuan Pegawai - SIAP Neglasari')

@section('admin-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <h1 class="text-2xl font-bold text-neglasari-dark">Pengajuan Pegawai</h1>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
            <p class="text-sm text-gray-500 font-medium">Menunggu</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
            <p class="text-sm text-gray-500 font-medium">Disetujui</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
            <p class="text-sm text-gray-500 font-medium">Ditolak</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
            <p class="text-sm text-gray-500 font-medium">Izin Sakit (Bulan Ini)</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['sick_this_month'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
            <p class="text-sm text-gray-500 font-medium">Cuti (Bulan Ini)</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['leave_this_month'] }}</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
        <form method="GET" action="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.index' : 'admin.leave-requests.index') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIP..." class="w-full rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 text-sm">
            </div>
            
            <select name="type" class="rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 text-sm">
                <option value="">Semua Jenis</option>
                @foreach(App\Enums\LeaveRequestType::cases() as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
            
            <select name="status" class="rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 text-sm">
                <option value="">Semua Status</option>
                @foreach(App\Enums\LeaveRequestStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
            
            <button type="submit" class="px-4 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl hover:bg-gray-300 transition text-sm">
                Filter
            </button>
            
            @if(request()->anyFilled(['search', 'type', 'status']))
                <a href="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.index' : 'admin.leave-requests.index') }}" class="px-4 py-2 text-red-600 hover:text-red-800 transition text-sm flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-neglasari-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-neglasari-border text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Pegawai</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Mulai</th>
                        <th class="px-6 py-4">Selesai</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Diajukan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-neglasari-dark">{{ $req->employee->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $req->employee->position->name }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-neglasari-text">{{ $req->type->label() }}</td>
                            <td class="px-6 py-4">{{ $req->start_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">{{ $req->end_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ $req->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $req->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $req->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $req->status->value === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ $req->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $req->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route(Auth::user()->hasRole('Kepala Desa') ? 'kades.leave-requests.show' : 'admin.leave-requests.show', $req) }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 text-neglasari-text font-medium rounded-lg text-xs transition">
                                    {{ $req->status->value === 'pending' ? 'Proses' : 'Detail' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada data pengajuan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaveRequests->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
