@extends('layouts.employee')

@section('title', '- Absen')

@section('employee-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-lg font-bold text-neglasari-dark mb-4">Absensi Hari Ini</h2>
        
        @if($attendance)
            @if($attendance->check_in_at)
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-neglasari-bg rounded-xl">
                        <div>
                            <p class="text-sm text-neglasari-text-secondary">Absen Masuk</p>
                            <p class="text-lg font-bold text-neglasari-text">{{ $attendance->check_in_at->format('H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $attendance->check_in_at->format('d M Y') }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{
                            $attendance->check_in_location_status === 'inside_radius' ? 'bg-green-100 text-green-800' : 
                            ($attendance->check_in_location_status === 'outside_radius' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                        }}">
                            {{ $attendance->check_in_location_status === 'inside_radius' ? 'Dalam Radius' : 'Luar Radius' }}
                        </span>
                    </div>
                    
                    @if($attendance->check_out_at)
                        <div class="flex justify-between items-center p-3 bg-neglasari-bg rounded-xl">
                            <div>
                                <p class="text-sm text-neglasari-text-secondary">Absen Pulang</p>
                                <p class="text-lg font-bold text-neglasari-text">{{ $attendance->check_out_at->format('H:i') }}</p>
                                <p class="text-xs text-gray-500">{{ $attendance->check_out_at->format('d M Y') }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{
                                $attendance->check_out_status === 'normal' ? 'bg-green-100 text-green-800' : 
                                ($attendance->check_out_status === 'early_leave' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')
                            }}">
                                {{ $attendance->check_out_status === 'normal' ? 'Normal' : ($attendance->check_out_status === 'early_leave' ? 'Pulang Awal' : 'Lembur') }}
                            </span>
                        </div>
                        
                        <div class="p-3 bg-neglasari-bg rounded-xl text-center">
                            <p class="text-sm text-neglasari-text-secondary">Durasi Kerja</p>
                            <p class="text-2xl font-bold text-neglasari-main">{{ floor($attendance->work_duration_minutes / 60) }} jam {{ $attendance->work_duration_minutes % 60 }} menit</p>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-neglasari-text-secondary">Anda belum absen pulang.</p>
                            <a href="{{ route('pegawai.attendance.checkout') }}" class="mt-2 inline-block px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                                Absen Pulang
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-sm text-neglasari-text-secondary">Anda belum melakukan absen masuk.</p>
                    <a href="{{ route('pegawai.attendance.checkin') }}" class="mt-2 inline-block px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                        Absen Masuk
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <p class="text-sm text-neglasari-text-secondary">Anda belum melakukan absen hari ini.</p>
                <a href="{{ route('pegawai.attendance.checkin') }}" class="mt-2 inline-block px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                    Absen Masuk
                </a>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-lg font-bold text-neglasari-dark mb-4">Lokasi Absen</h2>
        <div id="map" class="h-64 w-full rounded-xl border border-neglasari-border"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-lg font-bold text-neglasari-dark mb-4">Riwayat Absen Bulan Ini</h2>
        <div class="space-y-3">
            @forelse($monthlyAttendances as $att)
                <div class="flex items-center justify-between p-3 bg-neglasari-bg rounded-xl">
                    <div>
                        <p class="text-sm font-semibold text-neglasari-text">{{ $att->attendance_date->format('d M Y') }}</p>
                        <p class="text-xs text-neglasari-text-secondary">{{ $att->attendance_status->label() }}</p>
                    </div>
                    <div class="text-right">
                        @if($att->check_in_at)
                            <p class="text-xs text-neglasari-text">{{ $att->check_in_at->format('H:i') }}</p>
                        @else
                            <p class="text-xs text-gray-400">-</p>
                        @endif
                        @if($att->check_out_at)
                            <p class="text-xs text-neglasari-text">{{ $att->check_out_at->format('H:i') }}</p>
                        @else
                            <p class="text-xs text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-neglasari-text-secondary text-center py-4">Tidak ada riwayat absen bulan ini.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        const officeLocation = @json($officeLocation);
        const todayAttendance = @json($attendance);
        
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
        
        if (todayAttendance) {
            if (todayAttendance.check_in_latitude && todayAttendance.check_in_longitude) {
                L.marker([todayAttendance.check_in_latitude, todayAttendance.check_in_longitude], {
                    icon: L.divIcon({
                        className: 'text-green-500',
                        html: '✓',
                        iconSize: [24, 24]
                    })
                }).addTo(map)
                .bindPopup('<b>Absen Masuk</b><br>' + new Date(todayAttendance.check_in_at).toLocaleTimeString());
            }
            
            if (todayAttendance.check_out_latitude && todayAttendance.check_out_longitude) {
                L.marker([todayAttendance.check_out_latitude, todayAttendance.check_out_longitude], {
                    icon: L.divIcon({
                        className: 'text-red-500',
                        html: '✗',
                        iconSize: [24, 24]
                    })
                }).addTo(map)
                .bindPopup('<b>Absen Pulang</b><br>' + new Date(todayAttendance.check_out_at).toLocaleTimeString());
            }
        }
        
        map.fitBounds(officeCircle.getBounds());
    });
</script>
@endpush
@endsection
