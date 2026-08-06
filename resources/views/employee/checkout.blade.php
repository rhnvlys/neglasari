@extends('layouts.employee')

@section('title', '- Absen Pulang')

@section('employee-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <h2 class="text-lg font-bold text-neglasari-dark mb-4">Absen Pulang</h2>
        <p class="text-sm text-neglasari-text-secondary mb-6">Pastikan Anda berada di lokasi kantor dan ambil foto selfie untuk absen pulang.</p>
        
        <div id="map" class="h-64 w-full rounded-xl border border-neglasari-border mb-6"></div>
        
        <form id="checkoutForm" method="POST" action="{{ route('pegawai.attendance.checkout') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="accuracy" id="accuracy">
            
            <div class="mb-6">
                <label for="photo" class="block text-sm font-semibold text-neglasari-text mb-2">Foto Selfie</label>
                <div class="border-2 border-dashed border-neglasari-border rounded-xl p-4 text-center">
                    <div id="photoPreview" class="hidden">
                        <img id="previewImage" class="w-full h-48 object-cover rounded-lg" src="" alt="Preview Foto">
                    </div>
                    <div id="photoPlaceholder" class="flex flex-col items-center justify-center h-48">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-neglasari-text-secondary mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        <p class="text-sm text-neglasari-text-secondary">Ambil foto selfie</p>
                    </div>
                    <input type="file" name="photo" id="photo" accept="image/*" capture="user" class="hidden">
                    <button type="button" id="takePhotoBtn" class="mt-4 px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                        Ambil Foto
                    </button>
                </div>
                @error('photo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" id="submitBtn" class="px-8 py-3 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Absen Pulang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        const officeLocation = @json($officeLocation);
        const form = document.getElementById('checkoutForm');
        const submitBtn = document.getElementById('submitBtn');
        const takePhotoBtn = document.getElementById('takePhotoBtn');
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoPlaceholder = document.getElementById('photoPlaceholder');
        const previewImage = document.getElementById('previewImage');
        
        let currentPosition = null;
        let map = null;
        let userMarker = null;
        let officeMarker = null;
        let officeCircle = null;
        
        // Initialize map
        map = L.map('map').setView([officeLocation.latitude, officeLocation.longitude], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        officeMarker = L.marker([officeLocation.latitude, officeLocation.longitude]).addTo(map)
            .bindPopup('<b>Kantor Desa</b><br>' + officeLocation.name);
        
        officeCircle = L.circle([officeLocation.latitude, officeLocation.longitude], {
            color: '#1F5D42',
            fillColor: '#1F5D42',
            fillOpacity: 0.1,
            radius: officeLocation.radius_meters
        }).addTo(map);
        
        // Get current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentPosition = position;
                    updateLocationUI(position);
                },
                (error) => {
                    console.error("Error getting location:", error);
                    alert("Tidak dapat mengambil lokasi. Pastikan GPS aktif dan izinkan akses lokasi.");
                },
                { enableHighAccuracy: true }
            );
        }
        
        function updateLocationUI(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('accuracy').value = position.coords.accuracy;
            
            if (userMarker) {
                map.removeLayer(userMarker);
            }
            
            userMarker = L.marker([position.coords.latitude, position.coords.longitude], {
                icon: L.divIcon({
                    className: 'text-blue-500',
                    html: '📍',
                    iconSize: [24, 24]
                })
            }).addTo(map)
            .bindPopup('<b>Lokasi Anda</b><br>Akurasi: ' + Math.round(position.coords.accuracy) + 'm');
            
            map.setView([position.coords.latitude, position.coords.longitude], 17);
            
            // Check if inside radius
            const distance = calculateDistance(
                position.coords.latitude,
                position.coords.longitude,
                officeLocation.latitude,
                officeLocation.longitude
            );
            
            if (distance <= officeLocation.radius_meters) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
                alert("Anda berada di luar radius absensi yang diizinkan.");
            }
        }
        
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // metres
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;
            
            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                        Math.cos(φ1) * Math.cos(φ2) *
                        Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            
            return R * c;
        }
        
        // Handle photo capture
        takePhotoBtn.addEventListener('click', () => {
            photoInput.click();
        });
        
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
        
        // Watch position for continuous updates
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    updateLocationUI(position);
                },
                (error) => {
                    console.error("Error watching position:", error);
                },
                { enableHighAccuracy: true }
            );
        }
    });
</script>
@endpush
@endsection
