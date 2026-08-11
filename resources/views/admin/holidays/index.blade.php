@extends('layouts.admin')

@section('page-title', 'Kelola Hari Libur')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-neglasari-dark">Kelola Hari Libur & Cuti Bersama</h2>
                <p class="text-xs text-neglasari-text-secondary mt-1">Daftar hari libur nasional dan lokal Desa Neglasari</p>
            </div>
            <a href="{{ route('admin.holidays.create') }}" class="mt-4 md:mt-0 px-4 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150 text-sm inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Hari Libur
            </a>
        </div>
        
        <form method="GET" action="{{ route('admin.holidays.index') }}" class="mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama hari libur..." class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                <button type="submit" class="px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                    Cari
                </button>
            </div>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neglasari-border">
                <thead class="bg-neglasari-bg">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Nama Hari Libur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-neglasari-text uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neglasari-border">
                    @forelse($holidays as $holiday)
                        <tr class="hover:bg-neglasari-bg transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-neglasari-text">
                                <div class="font-bold text-neglasari-dark">{{ $holiday->name }}</div>
                                @if($holiday->description)
                                    <div class="text-xs text-neglasari-text-secondary">{{ $holiday->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-neglasari-text">
                                {{ \Carbon\Carbon::parse($holiday->start_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-neglasari-text">
                                {{ \Carbon\Carbon::parse($holiday->end_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-neglasari-text">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ is_object($holiday->type) ? $holiday->type->value : $holiday->type }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $holiday->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $holiday->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.holidays.edit', $holiday) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-neglasari-text-secondary">
                                Tidak ada data hari libur ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $holidays->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
