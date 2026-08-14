@extends('layouts.employee')

@section('title', '- Absen Pulang')

@section('employee-content')
<div class="space-y-5">
    <div class="bg-white rounded-2xl shadow-xl p-5 border border-neglasari-border">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-lg font-bold text-neglasari-dark">Absen Pulang Hari Ini</h2>
                <p class="text-xs text-neglasari-text-secondary">Pastikan lokasi GPS aktif dan sertakan foto swafoto terbaru.</p>
            </div>
            <span id="locationBadge" class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                Memuat GPS...
            </span>
        </div>
        
        <div id="map" class="h-56 w-full rounded-xl border border-neglasari-border mb-4 shadow-inner"></div>
        
        <form id="checkoutForm" method="POST" action="{{ route('pegawai.attendance.checkout') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="accuracy" id="accuracy">
            
            <div class="mb-5">
                <label for="photo" class="block text-xs font-bold text-neglasari-text uppercase tracking-wider mb-2">Foto Swafoto (Selfie)</label>
                <div class="border-2 border-dashed border-neglasari-border rounded-xl p-3 text-center bg-neglasari-bg/30">
                    <div id="photoPreview" class="hidden mb-3">
                        <img id="previewImage" class="w-full h-48 object-cover rounded-lg shadow-sm border border-neglasari-border" src="" alt="Preview Foto Selfie">
                    </div>
                    <div id="photoPlaceholder" class="flex flex-col items-center justify-center h-36">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-neglasari-main mb-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        <p class="text-xs font-semibold text-neglasari-text-secondary">Tekan tombol di bawah untuk ambil foto</p>
                    </div>
                    <input type="file" name="photo" id="photo" accept="image/*" capture="user" class="hidden">
                    <button type="button" id="takePhotoBtn" class="w-full py-2.5 bg-white border border-neglasari-main text-neglasari-main font-semibold text-xs rounded-xl shadow-sm hover:bg-neglasari-bg transition duration-150">
                        📸 Ambil / Pilih Swafoto
                    </button>
                </div>
                @error('photo')
                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <button type="submit" id="submitBtn" class="w-full py-3 px-6 bg-neglasari-main text-white font-bold text-sm rounded-xl shadow-lg hover:bg-neglasari-accent active:scale-95 transition duration-150 flex items-center justify-center space-x-2 disabled:opacity-50 cursor-pointer">
                    <span id="btnText">Kirim Absen Pulang</span>
                    <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const officeLocation = @json($officeLocation);
        const form = document.getElementById('checkoutForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const takePhotoBtn = document.getElementById('takePhotoBtn');
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoPlaceholder = document.getElementById('photoPlaceholder');
        const previewImage = document.getElementById('previewImage');
        const locationBadge = document.getElementById('locationBadge');
        
        let map = L.map('map').setView([officeLocation.latitude, officeLocation.longitude], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        
        L.marker([officeLocation.latitude, officeLocation.longitude]).addTo(map)
            .bindPopup('<b>Kantor Desa</b><br>' + officeLocation.name);
        
        const officeCircle = L.circle([officeLocation.latitude, officeLocation.longitude], {
            color: '#1F5D42',
            fillColor: '#1F5D42',
            fillOpacity: 0.15,
            radius: officeLocation.radius_meters || 1000
        }).addTo(map);
        
        let userMarker = null;

        document.getElementById('latitude').value = officeLocation.latitude;
        document.getElementById('longitude').value = officeLocation.longitude;
        document.getElementById('accuracy').value = 10;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => updateGPS(pos),
                (err) => {
                    console.warn("GPS warning:", err);
                    locationBadge.className = "px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800";
                    locationBadge.textContent = "GPS Standar";
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        function updateGPS(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const acc = Math.round(pos.coords.accuracy || 10);
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('accuracy').value = acc;

            if (userMarker) map.removeLayer(userMarker);
            userMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'text-2xl',
                    html: '📍',
                    iconSize: [24, 24]
                })
            }).addTo(map).bindPopup('<b>Posisi Anda</b><br>Akurasi: ' + acc + 'm');
            
            const dist = calculateDistance(lat, lng, officeLocation.latitude, officeLocation.longitude);
            if (dist <= (officeLocation.radius_meters || 1000)) {
                locationBadge.className = "px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800";
                locationBadge.textContent = "Dalam Radius (" + Math.round(dist) + "m)";
            } else {
                locationBadge.className = "px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800";
                locationBadge.textContent = "Luar Radius (" + Math.round(dist) + "m)";
            }
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        takePhotoBtn.addEventListener('click', () => photoInput.click());
        photoInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    photoPreview.classList.remove('hidden');
                    photoPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            btnText.textContent = "Mengirim Absen...";
            btnSpinner.classList.remove('hidden');
        });
    });
</script>
@endpush
@endsection
