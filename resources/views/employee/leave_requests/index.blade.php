@extends('layouts.employee')

@section('title', '- Pengajuan Saya')

@section('employee-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-neglasari-dark">Pengajuan Saya</h2>
        <a href="{{ route('pegawai.leave-requests.create') }}" class="px-4 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150 text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Pengajuan
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-neglasari-border">
        <form method="GET" action="{{ route('pegawai.leave-requests.index') }}" class="flex flex-col md:flex-row gap-3">
            <select name="type" class="rounded-xl border-neglasari-border shadow-sm focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 text-sm">
                <option value="">Semua Jenis</option>
                @foreach(App\Enums\LeaveRequestType::cases() as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
            
            <select name="status" class="rounded-xl border-neglasari-border shadow-sm focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 text-sm">
                <option value="">Semua Status</option>
                @foreach(App\Enums\LeaveRequestStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>

            <select name="year" class="rounded-xl border-neglasari-border shadow-sm focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 text-sm">
                <option value="">Semua Tahun</option>
                @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            
            <button type="submit" class="px-4 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl hover:bg-gray-300 transition duration-150 text-sm">
                Filter
            </button>
            
            @if(request()->anyFilled(['type', 'status', 'year']))
                <a href="{{ route('pegawai.leave-requests.index') }}" class="px-4 py-2 text-red-600 hover:text-red-800 transition duration-150 text-sm flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- List -->
    <div class="space-y-4">
        @forelse($leaveRequests as $req)
            <div class="bg-white rounded-2xl shadow-sm p-4 border border-neglasari-border">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full mb-2
                            {{ $req->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $req->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $req->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $req->status->value === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                        ">
                            {{ $req->status->label() }}
                        </span>
                        <h3 class="font-bold text-neglasari-dark">{{ $req->type->label() }}</h3>
                    </div>
                    <div class="text-right text-sm">
                        <p class="text-neglasari-text font-medium">{{ $req->start_date->format('d M Y') }}</p>
                        <p class="text-gray-500 text-xs">s/d {{ $req->end_date->format('d M Y') }}</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $req->reason }}</p>
                
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span class="text-xs text-gray-400">Diajukan: {{ $req->created_at->format('d M Y H:i') }}</span>
                    <div class="flex gap-2">
                        @if($req->status->value === 'pending')
                            <form action="{{ route('pegawai.leave-requests.cancel', $req) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-medium transition duration-150">Batal</button>
                            </form>
                        @endif
                        <a href="{{ route('pegawai.leave-requests.show', $req) }}" class="px-3 py-1.5 bg-neglasari-bg text-neglasari-text hover:bg-gray-200 rounded-lg text-xs font-medium transition duration-150">Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-neglasari-border">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p class="text-neglasari-text-secondary">Anda belum memiliki riwayat pengajuan.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $leaveRequests->links() }}
    </div>
</div>
@endsection
