@extends('layouts.admin')

@section('page-title', 'Tambah Lokasi Kantor')

@section('admin-content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-neglasari-border">
            <div>
                <h2 class="text-xl font-bold text-neglasari-dark">Tambah Lokasi Kantor Baru</h2>
                <p class="text-xs text-neglasari-text-secondary mt-1">Pilih lokasi kantor secara interaktif di peta (Google Maps / OpenStreetMap style)</p>
            </div>
            <a href="{{ route('admin.locations.index') }}" class="text-sm font-semibold text-neglasari-text-secondary hover:text-neglasari-main flex items-center bg-gray-100 px-3 py-1.5 rounded-xl transition">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Form Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Lokasi / Kantor <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Kantor Desa Neglasari" class="w-full px-4 py-2.5 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold text-neglasari-text mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="address" id="address" rows="3" required placeholder="Jl. Raya Desa Neglasari No. 01, Kec. Jatinunggal..." class="w-full px-4 py-2.5 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent shadow-sm">{{ old('address') }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="radius_meters" class="block text-sm font-semibold text-neglasari-text mb-1">Radius Toleransi (Meter) <span class="text-red-500">*</span></label>
                            <input type="number" name="radius_meters" id="radius_meters" value="{{ old('radius_meters', 100) }}" min="1" required class="w-full px-4 py-2.5 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent shadow-sm">
                            @error('radius_meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="maximum_accuracy_meters" class="block text-sm font-semibold text-neglasari-text mb-1">Max Akurasi GPS (Meter) <span class="text-red-500">*</span></label>
                            <input type="number" name="maximum_accuracy_meters" id="maximum_accuracy_meters" value="{{ old('maximum_accuracy_meters', 50) }}" min="1" required class="w-full px-4 py-2.5 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent shadow-sm">
                            @error('maximum_accuracy_meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Interactive Map Picker -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-bold text-neglasari-dark">📍 Pilih Titik Lokasi di Peta</label>
                        <button type="button" id="btn-detect-gps" class="text-xs bg-emerald-50 text-emerald-700 font-semibold px-3 py-1 rounded-lg border border-emerald-200 hover:bg-emerald-100 transition flex items-center gap-1">
                            <span>🎯</span> Deteksi Lokasi Saya
                        </button>
                    </div>

                    <!-- Search Box -->
                    <div class="flex gap-2">
                        <input type="text" id="map-search-input" placeholder="Cari nama lokasi/desa di peta..." class="w-full text-xs px-3 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <button type="button" id="btn-search-map" class="text-xs bg-neglasari-main text-white px-3 py-2 rounded-xl hover:bg-neglasari-accent transition font-semibold">Cari</button>
                    </div>

                    <div id="map-picker" class="w-full h-64 rounded-2xl border-2 border-neglasari-main/20 shadow-md relative z-10"></div>
                    <p class="text-[11px] text-gray-500 italic">* Klik atau geser pin pada peta untuk menentukan posisi kantor secara presisi.</p>
                </div>
            </div>

            <!-- Accordion / Backup Input Koordinat Manual -->
            <div x-data="{ showManual: false }" class="bg-gray-50 rounded-2xl p-4 border border-neglasari-border">
                <button type="button" @click="showManual = !showManual" class="w-full flex items-center justify-between text-xs font-bold text-neglasari-text">
                    <span>🛠️ Backup Input Koordinat Manual (Opsional / Terisi Otomatis)</span>
                    <span x-text="showManual ? '▲ Sembunyikan' : '▼ Tampilkan'"></span>
                </button>
                
                <div x-show="showManual" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-xs font-semibold text-neglasari-text mb-1">Latitude (Lintang)</label>
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', '-7.164300') }}" placeholder="-7.164300" class="w-full px-3 py-2 border border-neglasari-border rounded-xl font-mono text-xs focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>

                    <div>
                        <label for="longitude" class="block text-xs font-semibold text-neglasari-text mb-1">Longitude (Bujur)</label>
                        <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', '108.083200') }}" placeholder="108.083200" class="w-full px-3 py-2 border border-neglasari-border rounded-xl font-mono text-xs focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="requires_photo" value="1" {{ old('requires_photo', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Wajibkan Swafoto (Selfie) saat Absen</span>
                </label>
                <br>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="allow_outside_radius" value="1" {{ old('allow_outside_radius', false) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Izinkan Absen di Luar Radius (Dinas Luar)</span>
                </label>
                <br>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Status Lokasi Aktif</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-neglasari-border">
                <a href="{{ route('admin.locations.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-neglasari-main text-white font-semibold rounded-xl hover:bg-neglasari-accent transition shadow-md">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meters');

    let defaultLat = parseFloat(latInput.value) || -7.164300;
    let defaultLng = parseFloat(lngInput.value) || 108.083200;
    let defaultRadius = parseInt(radiusInput.value) || 100;

    const map = L.map('map-picker').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
    let circle = L.circle([defaultLat, defaultLng], {
        color: '#16a34a',
        fillColor: '#22c55e',
        fillOpacity: 0.25,
        radius: defaultRadius
    }).addTo(map);

    function updateCoords(lat, lng) {
        const roundedLat = parseFloat(lat).toFixed(6);
        const roundedLng = parseFloat(lng).toFixed(6);
        latInput.value = roundedLat;
        lngInput.value = roundedLng;
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
    }

    marker.on('dragend', function (e) {
        const latlng = marker.getLatLng();
        updateCoords(latlng.lat, latlng.lng);
    });

    map.on('click', function (e) {
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    radiusInput.addEventListener('input', function () {
        const rad = parseInt(this.value) || 100;
        circle.setRadius(rad);
    });

    document.getElementById('btn-detect-gps').addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                map.setView([lat, lng], 17);
                updateCoords(lat, lng);
            }, function () {
                alert('Gagal mendeteksi lokasi GPS. Pastikan izin lokasi aktif.');
            });
        } else {
            alert('Browser Anda tidak mendukung Geolocation.');
        }
    });

    document.getElementById('btn-search-map').addEventListener('click', function () {
        const query = document.getElementById('map-search-input').value;
        if (!query) return;
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    map.setView([lat, lon], 16);
                    updateCoords(lat, lon);
                } else {
                    alert('Lokasi tidak ditemukan. Coba kata kunci lain.');
                }
            }).catch(() => alert('Gagal menghubungi layanan pencarian lokasi.'));
    });
});
</script>
@endpush
@endsection
