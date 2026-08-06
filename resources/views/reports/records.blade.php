@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neglasari-dark">{{ $title }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'xlsx'])) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
               Export Excel
            </a>
            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'pdf'])) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
               Export PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pegawai</label>
                <select name="employee_id" class="w-full p-2 border rounded-md">
                    <option value="">Semua Pegawai</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
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
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal / Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail / Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $index => $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ isset($item->attendance_date) ? $item->attendance_date->toDateString() : ($item->start_date ? $item->start_date->toDateString() : '-') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->employee->full_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->employee->position->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($leaveRequest)
                                    <span class="font-semibold">{{ $item->type->label() }}</span> - {{ $item->status->label() }}
                                @else
                                    <span class="font-semibold">{{ $item->attendance_status->label() }}</span>
                                    @if($item->late_minutes) ({{ $item->late_minutes }} m) @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50">
            {{ $data->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
