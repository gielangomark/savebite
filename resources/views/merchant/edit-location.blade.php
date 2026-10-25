<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lokasi Toko - Save Bite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        #map { height: 400px; border-radius: 16px; border: 2px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .leaflet-container { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-emerald-50/30 to-teal-50/40 text-slate-800 min-h-screen antialiased">

    <!-- Top Accent Bar -->
    <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-amber-500 w-full"></div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-emerald-100/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('merchant.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-lg shadow-md">
                    🌱
                </div>
                <span class="text-lg font-black bg-gradient-to-r from-emerald-800 via-teal-700 to-emerald-900 bg-clip-text text-transparent">Save Bite</span>
            </a>
            <a href="{{ route('merchant.dashboard') }}" class="rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-extrabold text-slate-700 transition">
                ← Kembali
            </a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">📍 Atur Lokasi Toko</h1>
            <p class="text-sm font-semibold text-slate-500">Input koordinat toko Anda untuk memudahkan pembeli menemukan produk berdasarkan jarak terdekat.</p>
        </div>

        <!-- Alert Messages -->
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-400 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <span class="text-lg">✅</span>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm font-bold text-amber-800 shadow-md">
                <div class="flex items-center justify-between pb-2 border-b border-amber-200/60 mb-2">
                    <span class="text-xs uppercase tracking-wider">⚠️ Terjadi Kesalahan:</span>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-amber-500 text-lg">&times;</button>
                </div>
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        {{ $error }}
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Form Card -->
        <div class="rounded-[28px] bg-white border border-slate-200/90 p-8 shadow-xl shadow-slate-200/50">

            <form method="POST" action="{{ route('merchant.location.update') }}" class="space-y-6" id="locationForm">
                @csrf
                @method('PUT')

                <!-- Info Box -->
                <div class="rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/60 p-4 text-sm">
                    <div class="flex gap-3">
                        <span class="text-base shrink-0">💡</span>
                        <div class="space-y-1">
                            <p class="font-bold text-emerald-900">Cara Mengatur Lokasi:</p>
                            <ol class="text-emerald-800 space-y-1 list-decimal list-inside text-xs font-semibold">
                                <li>Peta sudah menampilkan lokasi default (Jakarta)</li>
                                <li><strong>Klik di peta</strong> untuk menandai lokasi toko Anda</li>
                                <li>Marker akan muncul dan koordinat otomatis terisi</li>
                                <li>Sesuaikan jika perlu, lalu klik "Simpan Lokasi"</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Interactive Map -->
                <div>
                    <label class="block text-sm font-extrabold text-slate-900 mb-3">🗺️ Pilih Lokasi Toko di Peta</label>
                    <div id="map"></div>
                    <p class="text-xs text-slate-500 mt-2">Klik di peta untuk menandai lokasi toko Anda</p>
                </div>

                <!-- Latitude Input -->
                <div>
                    <label for="latitude" class="block text-sm font-extrabold text-slate-900 mb-2.5">Latitude (Garis Lintang)</label>
                    <input 
                        type="number" 
                        id="latitude" 
                        name="latitude" 
                        step="0.00000001"
                        placeholder="-6.2088"
                        value="{{ old('latitude', $merchant->latitude ?? -6.2088) }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition text-sm font-semibold bg-slate-50/50"
                    />
                    <p class="text-xs text-slate-500 mt-1.5">Range: -90 hingga 90 (otomatis terisi saat klik peta)</p>
                </div>

                <!-- Longitude Input -->
                <div>
                    <label for="longitude" class="block text-sm font-extrabold text-slate-900 mb-2.5">Longitude (Garis Bujur)</label>
                    <input 
                        type="number" 
                        id="longitude" 
                        name="longitude" 
                        step="0.00000001"
                        placeholder="106.8456"
                        value="{{ old('longitude', $merchant->longitude ?? 106.8456) }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition text-sm font-semibold bg-slate-50/50"
                    />
                    <p class="text-xs text-slate-500 mt-1.5">Range: -180 hingga 180 (otomatis terisi saat klik peta)</p>
                </div>

                <!-- Current Location Display -->
                @if ($merchant->hasLocation())
                    <div class="rounded-xl bg-emerald-50/60 border border-emerald-200/60 p-4">
                        <p class="text-xs font-bold text-emerald-900 mb-2">📍 Lokasi Saat Ini:</p>
                        <p class="text-sm font-extrabold text-emerald-800 font-mono">{{ $merchant->latitude }}, {{ $merchant->longitude }}</p>
                    </div>
                @else
                    <div class="rounded-xl bg-amber-50/60 border border-amber-200/60 p-4">
                        <p class="text-xs font-bold text-amber-900">⚠️ Lokasi belum diatur. Pembeli tidak bisa melihat jarak terdekat.</p>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black rounded-xl shadow-md shadow-emerald-600/30 transition active:scale-95"
                    >
                        ✅ Simpan Lokasi
                    </button>
                    <a 
                        href="{{ route('merchant.dashboard') }}"
                        class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-xl transition active:scale-95 text-center"
                    >
                        Batal
                    </a>
                </div>

            </form>

        </div>

        <!-- Example Section -->
        <div class="mt-8 rounded-[28px] bg-slate-100/50 border border-slate-200 p-8">
            <div class="flex gap-3 mb-4">
                <span class="text-2xl">🗺️</span>
                <div>
                    <h3 class="font-black text-slate-900 mb-1">Contoh Koordinat Jakarta</h3>
                    <p class="text-sm text-slate-600 font-semibold">Referensi koordinat beberapa lokasi di Jakarta:</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-mono text-slate-700">
                <div class="bg-white p-3 rounded-lg border border-slate-200">
                    <p class="font-black text-slate-900 mb-1">Monas</p>
                    <p>-6.1753, 106.8249</p>
                </div>
                <div class="bg-white p-3 rounded-lg border border-slate-200">
                    <p class="font-black text-slate-900 mb-1">Kota Tua</p>
                    <p>-6.1358, 106.8119</p>
                </div>
                <div class="bg-white p-3 rounded-lg border border-slate-200">
                    <p class="font-black text-slate-900 mb-1">Senayan</p>
                    <p>-6.2276, 106.7872</p>
                </div>
                <div class="bg-white p-3 rounded-lg border border-slate-200">
                    <p class="font-black text-slate-900 mb-1">Bandara CGK</p>
                    <p>-6.1256, 106.6590</p>
                </div>
            </div>
        </div>

    </main>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const locationForm = document.getElementById('locationForm');

            // Default to Jakarta if no location set
            const defaultLat = parseFloat(latInput.value) || -6.2088;
            const defaultLng = parseFloat(lngInput.value) || 106.8456;

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Initialize marker
            let marker = null;

            // Function to create/update marker
            function updateMarker(lat, lng, isUserMarked = false) {
                if (marker) {
                    map.removeLayer(marker);
                }

                const markerColor = isUserMarked ? 'green' : 'blue';
                const iconUrl = `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${markerColor}.png`;

                marker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: iconUrl,
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map);

                if (isUserMarked) {
                    marker.bindPopup(`📍 Lokasi Toko Baru<br><code>${lat.toFixed(8)}, ${lng.toFixed(8)}</code>`).openPopup();
                } else {
                    marker.bindPopup(`📍 Lokasi Toko Saat Ini`);
                }

                return marker;
            }

            // Show initial marker if exists
            if (latInput.value && lngInput.value) {
                updateMarker(parseFloat(latInput.value), parseFloat(lngInput.value), false);
            }

            // Handle map click to set location
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Update input fields with proper precision
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);

                // Update marker
                updateMarker(lat, lng, true);

                // Animate to location
                map.setView([lat, lng], 15);
            });

            // Handle manual latitude input change
            latInput.addEventListener('change', function() {
                const lat = parseFloat(this.value);
                const lng = parseFloat(lngInput.value);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                        updateMarker(lat, lng, true);
                        map.setView([lat, lng], 15);
                    }
                }
            });

            // Handle manual longitude input change
            lngInput.addEventListener('change', function() {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(this.value);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                        updateMarker(lat, lng, true);
                        map.setView([lat, lng], 15);
                    }
                }
            });

            // Validate form before submit
            locationForm.addEventListener('submit', function(e) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);

                if (isNaN(lat) || isNaN(lng)) {
                    e.preventDefault();
                    alert('❌ Pilih lokasi di peta atau input koordinat yang valid!');
                    return false;
                }

                if (lat < -90 || lat > 90) {
                    e.preventDefault();
                    alert('❌ Latitude harus antara -90 dan 90');
                    return false;
                }

                if (lng < -180 || lng > 180) {
                    e.preventDefault();
                    alert('❌ Longitude harus antara -180 dan 180');
                    return false;
                }

                // If validation passes, form will submit normally
                return true;
            });
        });
    </script>

@include('components.live-clock')
</body>
</html>
