@extends('layouts.admin')

@section('page-title', 'Detail Absensi')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-xl font-bold text-neglasari-dark">Detail Absensi</h2>
            <div class="flex items-center space-x-2 mt-4 md:mt-0">
                @if($attendance->check_in_at && !$attendance->check_out_at)
                    <button onclick="confirmCheckout('{{ route('admin.attendances.manual-checkout', $attendance) }}')" class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-xl shadow-md hover:bg-yellow-600 transition duration-150 text-sm">
                        Absen Pulang Manual
                    </button>
                @endif
                <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150 text-sm">
                    Kembali
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-neglasari-bg rounded-xl p-4">
                <h3 class="text-lg font-bold text-neglasari-dark mb-3">Informasi Pegawai</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-neglasari-text-secondary">Nama</span>
                        <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->employee->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-neglasari-text-secondary">Jabatan</span>
                        <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->employee->position->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-neglasari-text-secondary">Nomor Pegawai</span>
                        <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->employee->employee_number }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-neglasari-bg rounded-xl p-4">
                <h3 class="text-lg font-bold text-neglasari-dark mb-3">Informasi Absensi</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-neglasari-text-secondary">Tanggal</span>
                        <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->attendance_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-neglasari-text-secondary">Status</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                            $attendance->attendance_status === 'present' ? 'bg-green-100 text-green-800' :
                            ($attendance->attendance_status === 'late' ? 'bg-yellow-100 text-yellow-800' :
                            ($attendance->attendance_status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))
                        }}">
                            {{ $attendance->attendance_status->label() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-neglasari-text-secondary">Durasi Kerja</span>
                        <span class="text-sm font-semibold text-neglasari-text">
                            @if($attendance->work_duration_minutes > 0)
                                {{ floor($attendance->work_duration_minutes / 60) }} jam {{ $attendance->work_duration_minutes % 60 }} menit
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-neglasari-bg rounded-xl p-4">
                <h3 class="text-lg font-bold text-neglasari-dark mb-3">Absen Masuk</h3>
                @if($attendance->check_in_at)
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Waktu</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->check_in_at->format('H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Status Lokasi</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                                $attendance->check_in_location_status === 'inside_radius' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                            }}">
                                {{ $attendance->check_in_location_status === 'inside_radius' ? 'Dalam Radius' : 'Luar Radius' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Jarak dari Kantor</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ number_format($attendance->check_in_distance, 2) }} meter</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Akurasi GPS</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ number_format($attendance->check_in_accuracy, 2) }} meter</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">IP Address</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->check_in_ip ?? '-' }}</span>
                        </div>
                        @if($attendance->check_in_photo_url)
                            <div class="mt-4">
                                <p class="text-sm text-neglasari-text-secondary mb-2 font-semibold">Foto Absen Masuk</p>
                                <a href="{{ $attendance->check_in_photo_url }}" target="_blank" class="block">
                                    <img src="{{ $attendance->check_in_photo_url }}" alt="Foto Absen Masuk" class="w-full max-h-64 object-cover rounded-lg border border-neglasari-border hover:opacity-90 transition">
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-neglasari-text-secondary">Belum melakukan absen masuk.</p>
                @endif
            </div>
            
            <div class="bg-neglasari-bg rounded-xl p-4">
                <h3 class="text-lg font-bold text-neglasari-dark mb-3">Absen Pulang</h3>
                @if($attendance->check_out_at)
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Waktu</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->check_out_at->format('H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Status Lokasi</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                                $attendance->check_out_location_status === 'inside_radius' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                            }}">
                                {{ $attendance->check_out_location_status === 'inside_radius' ? 'Dalam Radius' : 'Luar Radius' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Jarak dari Kantor</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ number_format($attendance->check_out_distance, 2) }} meter</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Akurasi GPS</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ number_format($attendance->check_out_accuracy, 2) }} meter</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">Status Pulang</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                                $attendance->check_out_status === 'normal' ? 'bg-green-100 text-green-800' :
                                ($attendance->check_out_status === 'early_leave' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')
                            }}">
                                {{ $attendance->check_out_status === 'normal' ? 'Normal' : ($attendance->check_out_status === 'early_leave' ? 'Pulang Awal' : 'Lembur') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-neglasari-text-secondary">IP Address</span>
                            <span class="text-sm font-semibold text-neglasari-text">{{ $attendance->check_out_ip ?? '-' }}</span>
                        </div>
                        @if($attendance->check_out_photo_url)
                            <div class="mt-4">
                                <p class="text-sm text-neglasari-text-secondary mb-2 font-semibold">Foto Absen Pulang</p>
                                <a href="{{ $attendance->check_out_photo_url }}" target="_blank" class="block">
                                    <img src="{{ $attendance->check_out_photo_url }}" alt="Foto Absen Pulang" class="w-full max-h-64 object-cover rounded-lg border border-neglasari-border hover:opacity-90 transition">
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-neglasari-text-secondary">Belum melakukan absen pulang.</p>
                @endif
            </div>
        </div>
        
        <div class="bg-neglasari-bg rounded-xl p-4">
            <h3 class="text-lg font-bold text-neglasari-dark mb-3">Lokasi Absen</h3>
            <div id="map" class="h-96 w-full rounded-xl border border-neglasari-border"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmCheckout(url) {
        if (confirm('Apakah Anda yakin ingin melakukan absen pulang manual untuk pegawai ini?')) {
            window.location.href = url;
        }
    }
    
    document.addEventListener('alpine:init', () => {
        const officeLocation = @json($officeLocation);
        const attendance = @json($attendance);
        
        const map = L.map('map').setView([officeLocation.latitude, officeLocation.longitude], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        const officeMarker = L.marker([officeLocation.latitude, officeLocation.longitude]).addTo(map)
            .bindPopup('<b>Kantor Desa</b><br>' + officeLocation.name);
        
        const officeCircle = L.circle([officeLocation.latitude, officeLocation.longitude], {
            color: '#1F5D42',
            fillColor: '#1F5D42',
            fillOpacity: 0.1,
            radius: officeLocation.radius_meters
        }).addTo(map);
        
        if (attendance.check_in_latitude && attendance.check_in_longitude) {
            L.marker([attendance.check_in_latitude, attendance.check_in_longitude], {
                icon: L.divIcon({
                    className: 'text-green-500',
                    html: '✓',
                    iconSize: [24, 24]
                })
            }).addTo(map)
            .bindPopup('<b>Absen Masuk</b><br>' + new Date(attendance.check_in_at).toLocaleTimeString());
        }
        
        if (attendance.check_out_latitude && attendance.check_out_longitude) {
            L.marker([attendance.check_out_latitude, attendance.check_out_longitude], {
                icon: L.divIcon({
                    className: 'text-red-500',
                    html: '✗',
                    iconSize: [24, 24]
                })
            }).addTo(map)
            .bindPopup('<b>Absen Pulang</b><br>' + new Date(attendance.check_out_at).toLocaleTimeString());
        }
        
        map.fitBounds(officeCircle.getBounds());
    });
</script>
@endpush
@endsection
