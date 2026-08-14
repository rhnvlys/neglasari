@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neglasari-dark">{{ $title }}</h1>
        @if(auth()->user()?->hasRole(['Admin', 'Super Admin', 'Admin Desa']))
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'xlsx'])) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm font-semibold">
                   Export Excel
                </a>
                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'pdf'])) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm font-semibold">
                   Export PDF
                </a>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="month" class="w-full p-2 border rounded-md">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ request('year', now()->year) }}" class="w-full p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                <select name="position_id" class="w-full p-2 border rounded-md">
                    <option value="">Semua Jabatan</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-neglasari-main text-white rounded-md hover:bg-neglasari-dark transition">Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">No</th>
                        <th class="px-3 py-2 text-left">Pegawai</th>
                        <th class="px-3 py-2 text-left">Jabatan</th>
                        <th class="px-3 py-2 text-center">Hadir</th>
                        <th class="px-3 py-2 text-center">Telat</th>
                        <th class="px-3 py-2 text-center">Izin</th>
                        <th class="px-3 py-2 text-center">Sakit</th>
                        <th class="px-3 py-2 text-center">Cuti</th>
                        <th class="px-3 py-2 text-center">DL/TL</th>
                        <th class="px-3 py-2 text-center">Alpa</th>
                        <th class="px-3 py-2 text-center">Fisik %</th>
                        <th class="px-3 py-2 text-center">Total %</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $index => $row)
                        <tr>
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $row['employee']->full_name }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $row['employee']->position->name }}</td>
                            <td class="px-3 py-2 text-center text-green-700 font-semibold">{{ $row['present_on_time'] }}</td>
                            <td class="px-3 py-2 text-center text-amber-700 font-semibold">{{ $row['late'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $row['permission'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $row['sick'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $row['leave'] }}</td>
                            <td class="px-3 py-2 text-center">{{ $row['official_duty'] + $row['field_assignment'] }}</td>
                            <td class="px-3 py-2 text-center text-red-700 font-semibold">{{ $row['absent'] }}</td>
                            <td class="px-3 py-2 text-center font-bold">{{ number_format($row['physical_present_percent'], 1) }}%</td>
                            <td class="px-3 py-2 text-center font-bold text-neglasari-dark">{{ number_format($row['admin_present_percent'], 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-3 py-4 text-center text-gray-500">Tidak ada rekap absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
