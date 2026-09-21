@once
    @push('css')
        <style>
            .token-location-map { height: 300px; overflow: hidden; border: 1px solid #dce8df; border-radius: 12px; box-shadow: 0 8px 20px rgba(23, 70, 50, .08); }
            .token-location-help { color: #718096; font-size: .78rem; }
            .token-coordinate-inputs { margin-top: 1rem; }
            .token-map-pin { width: 24px; height: 24px; border: 3px solid #fff; border-radius: 50% 50% 50% 0; background: #198754; box-shadow: 0 3px 9px rgba(23, 70, 50, .35); transform: rotate(-45deg); }
            .token-map-pin::after { content: ''; position: absolute; width: 7px; height: 7px; top: 6px; left: 6px; border-radius: 50%; background: #fff; }
        </style>
    @endpush
@endonce

@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="token-label">Label kegiatan</label>
        <input id="token-label" name="label" value="{{ old('label', $token->label ?? '') }}" class="form-control @error('label') is-invalid @enderror" required>
        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="token-event-date">Tanggal kegiatan</label>
        <input id="token-event-date" type="date" name="event_date" value="{{ old('event_date', isset($token) && $token->event_date ? $token->event_date->format('Y-m-d') : '') }}" class="form-control @error('event_date') is-invalid @enderror" required>
        @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="token-expires-at">Berlaku sampai <span class="text-muted fw-normal">(opsional)</span></label>
        <input id="token-expires-at" type="datetime-local" name="expires_at" value="{{ old('expires_at', isset($token) && $token->expires_at ? $token->expires_at->format('Y-m-d\TH:i') : '') }}" class="form-control @error('expires_at') is-invalid @enderror">
        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="token-location-name">Lokasi default</label>
        <input id="token-location-name" name="location_name" value="{{ old('location_name', $token->location_name ?? '') }}" class="form-control @error('location_name') is-invalid @enderror" placeholder="Contoh: Tahura Ngurah Rai, Denpasar" required>
        @error('location_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label d-flex justify-content-between" for="token-location-map">
            <span>Tentukan titik lokasi</span>
            <span class="token-location-help"><i class="bi bi-hand-index-thumb me-1"></i>Klik peta untuk memilih koordinat</span>
        </label>
        <div id="token-location-map" class="token-location-map"></div>
    </div>
    <div class="col-md-6 token-coordinate-inputs">
        <label class="form-label" for="token-latitude">Latitude</label>
        <input id="token-latitude" name="latitude" value="{{ old('latitude', $token->latitude ?? '-8.6905000') }}" class="form-control @error('latitude') is-invalid @enderror" required>
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 token-coordinate-inputs">
        <label class="form-label" for="token-longitude">Longitude</label>
        <input id="token-longitude" name="longitude" value="{{ old('longitude', $token->longitude ?? '115.2126000') }}" class="form-control @error('longitude') is-invalid @enderror" required>
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if(isset($token))
        <div class="col-12">
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $token->is_active))>
                <label class="form-check-label">Token aktif</label>
            </div>
        </div>
    @endif
</div>
<p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Token baru dibuat otomatis dan ditampilkan sekali setelah disimpan. Gunakan QR Code tersebut untuk peserta.</p>

@once
    @push('js')
        @if ($googleMapsApiKey ?? config('services.google_maps.key'))
            <script>
                let tokenLocationMap;

                function initTokenLocationMap() {
                const mapElement = document.getElementById('token-location-map');
                const latitudeInput = document.getElementById('token-latitude');
                const longitudeInput = document.getElementById('token-longitude');

                if (!mapElement || !latitudeInput || !longitudeInput || typeof google === 'undefined') {
                    return;
                }

                const defaultLatitude = -8.6905;
                const defaultLongitude = 115.2126;
                const parsedLatitude = Number.parseFloat(latitudeInput.value);
                const parsedLongitude = Number.parseFloat(longitudeInput.value);
                const initialLatitude = Number.isFinite(parsedLatitude) ? parsedLatitude : defaultLatitude;
                const initialLongitude = Number.isFinite(parsedLongitude) ? parsedLongitude : defaultLongitude;
                const baliBounds = new google.maps.LatLngBounds(
                    { lat: -8.85, lng: 114.40 },
                    { lat: -8.05, lng: 115.75 },
                );
                const map = new google.maps.Map(mapElement, {
                    center: { lat: initialLatitude, lng: initialLongitude },
                    zoom: 11,
                    restriction: { latLngBounds: baliBounds, strictBounds: false },
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                });
                tokenLocationMap = map;

                const marker = new google.maps.Marker({
                    position: { lat: initialLatitude, lng: initialLongitude },
                    map,
                    draggable: true,
                    title: 'Lokasi penanaman',
                });
                const infoWindow = new google.maps.InfoWindow({ content: '<strong>Lokasi terpilih</strong><br>Klik atau geser pin untuk mengubah titik.' });

                const updateCoordinates = (latitude, longitude) => {
                    latitudeInput.value = latitude.toFixed(7);
                    longitudeInput.value = longitude.toFixed(7);
                    const position = { lat: latitude, lng: longitude };
                    marker.setPosition(position);
                };

                const moveMarkerFromInputs = () => {
                    const latitude = Number.parseFloat(latitudeInput.value);
                    const longitude = Number.parseFloat(longitudeInput.value);

                    if (Number.isFinite(latitude) && Number.isFinite(longitude)) {
                        const position = { lat: latitude, lng: longitude };
                        marker.setPosition(position);
                        map.panTo(position);
                    }
                };

                map.addListener('click', (event) => {
                    updateCoordinates(event.latLng.lat(), event.latLng.lng());
                    infoWindow.open({ anchor: marker, map });
                });
                marker.addListener('dragend', () => {
                    const position = marker.getPosition();
                    updateCoordinates(position.lat(), position.lng());
                    infoWindow.open({ anchor: marker, map });
                });
                latitudeInput.addEventListener('change', moveMarkerFromInputs);
                longitudeInput.addEventListener('change', moveMarkerFromInputs);

                document.querySelectorAll('[data-bs-target="#createTokenModal"]').forEach((button) => {
                    button.addEventListener('click', () => window.setTimeout(() => google.maps.event.trigger(map, 'resize'), 250));
                });
                document.querySelectorAll('#createTokenModal').forEach((modal) => {
                    modal.addEventListener('shown.bs.modal', () => google.maps.event.trigger(map, 'resize'));
                });
                window.setTimeout(() => google.maps.event.trigger(map, 'resize'), 250);
                }
            </script>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($googleMapsApiKey ?? config('services.google_maps.key')) }}&callback=initTokenLocationMap"></script>
        @else
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const mapElement = document.getElementById('token-location-map');
                    if (mapElement) {
                        mapElement.innerHTML = '<div class="alert alert-warning m-3"><i class="bi bi-exclamation-triangle me-1"></i>Google Maps API key belum dikonfigurasi. Isi <code>GOOGLE_MAPS_API_KEY</code> pada file <code>.env</code>.</div>';
                    }
                });
            </script>
        @endif
    @endpush
@endonce
