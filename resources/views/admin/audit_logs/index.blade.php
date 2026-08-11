@extends('layouts.admin')

@section('page-title', 'Log Aktivitas Sistem')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-neglasari-dark">Log Aktivitas & Audit Trail</h2>
                <p class="text-xs text-neglasari-text-secondary mt-1">Catatan riwayat tindakan pengguna dalam sistem SIAP Neglasari</p>
            </div>
        </div>
        
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari deskripsi, IP, atau aksi..." class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                <div>
                    <select name="module" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                        Filter Log
                    </button>
                </div>
            </div>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neglasari-border">
                <thead class="bg-neglasari-bg">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-neglasari-text uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neglasari-border">
                    @forelse($logs as $log)
                        <tr class="hover:bg-neglasari-bg transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-mono text-neglasari-text">
                                {{ $log->created_at?->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-neglasari-text font-bold">
                                {{ $log->user?->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-neglasari-text">
                                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-800 font-mono font-semibold">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-bold">
                                @if($log->action === 'CREATE' || $log->action === 'LOGIN')
                                    <span class="text-green-700 bg-green-50 px-2 py-0.5 rounded border border-green-200">{{ $log->action }}</span>
                                @elseif($log->action === 'UPDATE')
                                    <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $log->action }}</span>
                                @elseif($log->action === 'DELETE')
                                    <span class="text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-200">{{ $log->action }}</span>
                                @else
                                    <span class="text-gray-700 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-neglasari-text">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-xs font-mono text-neglasari-text-secondary">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-neglasari-text-secondary">
                                Belum ada catatan aktivitas sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
