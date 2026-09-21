@php($plantTypeOptions = $plantTypes->isNotEmpty() ? $plantTypes : ['Mahoni', 'Trembesi', 'Mangrove', 'Beringin', 'Bambu', 'Nyamplung'])
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontribusi Penanaman · {{ $applicationName }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root { --forest: #176246; --forest-dark: #104936; --mint: #eaf7ef; --ink: #19372d; --muted: #71857c; }
        body { min-height: 100vh; background: #f5faf7; color: var(--ink); }
        .page-shell { max-width: 1180px; margin: 0 auto; padding: 1.5rem 1rem 4rem; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
        .brand { display: flex; min-width: 0; align-items: center; gap: .65rem; color: var(--forest); }
        .brand i { display: inline-flex; width: 36px; height: 36px; flex: 0 0 36px; align-items: center; justify-content: center; border-radius: 11px; color: #fff; background: var(--forest); box-shadow: 0 5px 12px rgba(23, 98, 70, .16); }
        .brand-copy { min-width: 0; }
        .brand-name { display: block; font-size: 1.05rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; }
        .brand-tagline { display: block; margin-top: .2rem; overflow: hidden; color: var(--muted); font-size: .7rem; line-height: 1.2; text-overflow: ellipsis; white-space: nowrap; }
        .token-pill { padding: .55rem .8rem; border: 1px solid #d7eadf; border-radius: 999px; color: var(--forest); background: #fff; font-size: .72rem; font-weight: 700; }
        .event-context { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; margin-bottom: 1.25rem; padding: 1rem 1.15rem; border: 1px solid #d7eadf; border-radius: 15px; background: #fff; box-shadow: 0 6px 18px rgba(23, 70, 50, .04); }
        .event-context__item { display: flex; align-items: center; gap: .65rem; min-width: 0; }
        .event-context__item > span:last-child { min-width: 0; flex: 1; }
        .event-context__icon { display: inline-flex; width: 31px; height: 31px; flex: 0 0 31px; align-items: center; justify-content: center; border-radius: 9px; color: var(--forest); background: var(--mint); font-size: .8rem; }
        .event-context__label { display: block; color: var(--muted); font-size: .65rem; }
        .event-context__value { display: block; margin-top: .12rem; overflow-wrap: anywhere; color: var(--ink); font-size: .78rem; font-weight: 700; line-height: 1.35; }
        .hero { position: relative; overflow: hidden; padding: 2rem; border-radius: 22px; color: #fff; background: linear-gradient(115deg, #12543d 0%, #277c5d 100%); box-shadow: 0 14px 35px rgba(23, 98, 70, .16); }
        .hero:after { position: absolute; right: -5rem; bottom: -7rem; width: 20rem; height: 20rem; border: 35px solid rgba(255,255,255,.08); border-radius: 50%; content: ''; }
        .hero > * { position: relative; z-index: 1; }
        .hero h1 { max-width: 600px; margin-bottom: .55rem; font-size: clamp(1.55rem, 3vw, 2.25rem); font-weight: 800; letter-spacing: -.04em; }
        .hero p { max-width: 590px; margin: 0; color: rgba(255,255,255,.8); font-size: .9rem; line-height: 1.65; }
        .stepbar { display: flex; flex-wrap: wrap; gap: .5rem 1.4rem; margin-top: 1.4rem; }
        .stepbar span { color: rgba(255,255,255,.72); font-size: .72rem; }
        .stepbar b { display: inline-flex; width: 22px; height: 22px; align-items: center; justify-content: center; margin-right: .35rem; border-radius: 50%; color: var(--forest); background: #fff; font-size: .7rem; }
        .main-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(280px, .75fr); gap: 1.25rem; align-items: start; margin-top: 1.25rem; }
        .form-card, .side-card { border: 1px solid #deece3; border-radius: 18px; background: #fff; box-shadow: 0 8px 25px rgba(30, 76, 57, .05); }
        .form-card-header { padding: 1.35rem 1.5rem; border-bottom: 1px solid #edf3ef; }
        .form-card-header h2 { margin: 0 0 .3rem; font-size: 1.1rem; font-weight: 800; }
        .form-card-header p, .side-card p { margin: 0; color: var(--muted); font-size: .78rem; }
        .form-card-body { padding: 1.5rem; }
        .section-heading { display: flex; align-items: center; gap: .65rem; margin: .25rem 0 1rem; color: var(--forest); font-size: .72rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .section-heading:not(:first-child) { margin-top: 1.8rem; }
        .section-heading b { display: inline-flex; width: 27px; height: 27px; align-items: center; justify-content: center; border-radius: 9px; color: #fff; background: var(--forest); font-size: .72rem; }
        .form-label { margin-bottom: .4rem; color: #365449; font-size: .75rem; font-weight: 700; }
        .form-control, .form-select { min-height: 44px; padding: .65rem .8rem; border-color: #d8e8de; border-radius: 10px; color: var(--ink); font-size: .83rem; }
        .form-control:focus, .form-select:focus { border-color: #6bb18d; box-shadow: 0 0 0 .2rem rgba(39, 124, 93, .12); }
        .form-text { color: #8a9b94; font-size: .7rem; }
        .map { height: 310px; overflow: hidden; border: 1px solid #dbe9e0; border-radius: 13px; }
        .map-help { padding: .75rem .85rem; border-radius: 10px; color: #537366; background: var(--mint); font-size: .72rem; }
        .location-status { color: var(--forest); font-size: .72rem; }
        .upload-box { display: block; padding: 1.15rem; border: 1.5px dashed #9bc8ad; border-radius: 13px; color: #54776a; background: #f8fcf9; text-align: center; cursor: pointer; transition: .2s ease; }
        .upload-box:hover { border-color: var(--forest); background: var(--mint); }
        .upload-box i { display: block; margin-bottom: .35rem; color: var(--forest); font-size: 1.65rem; }
        .upload-box strong { display: block; color: #315a49; font-size: .8rem; }
        .upload-box small { font-size: .68rem; }
        .preview { display: block; max-width: 100%; max-height: 180px; margin: .75rem auto 0; border-radius: 10px; }
        .form-footer { padding: 1.25rem 1.5rem 1.5rem; border-top: 1px solid #edf3ef; }
        .btn-submit { padding: .75rem 1.1rem; border: 0; border-radius: 10px; color: #fff; background: var(--forest); font-size: .82rem; font-weight: 700; }
        .btn-submit:hover { color: #fff; background: var(--forest-dark); }
        .btn-submit:disabled { cursor: not-allowed; opacity: .55; }
        .side-card { position: sticky; top: 1rem; padding: 1.25rem; }
        .side-card h3 { margin: .7rem 0 .25rem; font-size: 1rem; font-weight: 800; }
        .side-badge { display: inline-block; padding: .4rem .65rem; border-radius: 999px; color: var(--forest); background: var(--mint); font-size: .68rem; font-weight: 800; }
        .check-list { display: grid; gap: .8rem; margin: 1.15rem 0; padding: 0; list-style: none; }
        .check-list li { display: flex; gap: .65rem; align-items: flex-start; color: #5f766b; font-size: .75rem; line-height: 1.45; }
        .check-list i { color: #2b9a6b; font-size: .95rem; }
        .impact { display: flex; align-items: center; gap: .8rem; margin-top: 1rem; padding: .8rem; border-radius: 12px; background: #fff8e8; }
        .impact i { color: #d88a22; font-size: 1.3rem; }
        .impact small { display: block; color: #8c7a58; font-size: .65rem; }
        .impact strong { color: #70521d; font-size: 1rem; }
        .alert { border: 0; border-radius: 12px; font-size: .78rem; }
        @media (max-width: 800px) { .main-grid { grid-template-columns: 1fr; } .side-card { position: static; } }
        @media (max-width: 520px) { .page-shell { padding-top: 1rem; } .topbar { align-items: flex-start; } .token-pill { max-width: 42%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; } .event-context { grid-template-columns: 1fr; } .hero, .form-card-body { padding: 1.2rem; } .form-card-header, .form-footer { padding-left: 1.2rem; padding-right: 1.2rem; } .map { height: 270px; } }
    </style>
</head>
<body>
<main class="page-shell">
    <header class="topbar"><div class="brand"><i class="bi bi-tree-fill"></i><span class="brand-copy"><span class="brand-name">{{ $applicationName }}</span><span class="brand-tagline">{{ $applicationTagline }}</span></span></div><span class="token-pill"><i class="bi bi-shield-check me-1"></i>{{ $accessToken->label }}</span></header>
    <section class="event-context" aria-label="Informasi kegiatan penanaman">
        <div class="event-context__item"><span class="event-context__icon"><i class="bi bi-calendar-event"></i></span><span><span class="event-context__label">Tanggal kegiatan</span><span class="event-context__value">{{ $eventDate?->format('d M Y') ?? 'Belum ditentukan' }}</span></span></div>
        <div class="event-context__item"><span class="event-context__icon"><i class="bi bi-geo-alt"></i></span><span><span class="event-context__label">Lokasi kegiatan</span><span class="event-context__value">{{ $accessToken->location_name }}</span></span></div>
    </section>
    @if(session('success'))<div class="alert alert-success bg-success-subtle text-success mb-3"><i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger bg-danger-subtle text-danger mb-3"><i class="bi bi-exclamation-circle-fill me-1"></i>Periksa kembali data yang ditandai sebelum mengirim.</div>@endif
    <section class="hero"><h1>{{ $applicationName }}</h1><p>{{ $applicationTagline }}</p><div class="stepbar" aria-label="Tahapan pengisian form"><span><b>1</b>Identitas</span><span><b>2</b>Penanaman</span><span><b>3</b>Lokasi</span><span><b>4</b>Dokumentasi</span></div></section>
    <div class="main-grid">
        <form class="form-card" method="POST" action="{{ route('planting.store', ['token' => $token]) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-card-header"><h2>Catat penanaman pohon</h2><p>Isi kolom bertanda <span class="text-danger">*</span>. Perkiraan waktu pengisian 2–3 menit.</p></div>
            <div class="form-card-body">
                <div class="section-heading"><b>1</b>Identitas penanam</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="name">Nama lengkap <span class="text-danger">*</span></label><input id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" autocomplete="name" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="phone">Nomor telepon <span class="text-danger">*</span></label><input id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" autocomplete="tel" required>@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="email">Email <span class="text-muted fw-normal">(opsional)</span></label><input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="organization">Instansi / komunitas</label><select id="organization" name="organization" class="form-select"><option value="">Pilih instansi / komunitas</option>@foreach($agencies as $agency)@php($agencyName = $agency->short_name ?: $agency->name)<option value="{{ $agencyName }}" @selected(old('organization') === $agencyName)>{{ $agencyName }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label" for="job_title">Jabatan / peran</label><input id="job_title" name="job_title" value="{{ old('job_title') }}" class="form-control" placeholder="Contoh: Relawan"></div>
                </div>
                <div class="section-heading"><b>2</b>Detail penanaman</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="planted_at">Tanggal penanaman <span class="text-danger">*</span></label><input type="date" id="planted_at" name="planted_at" value="{{ old('planted_at', now()->format('Y-m-d')) }}" class="form-control @error('planted_at') is-invalid @enderror" required>@error('planted_at')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="plant_type">Jenis tanaman <span class="text-danger">*</span></label><select id="plant_type" name="plant_type" class="form-select @error('plant_type') is-invalid @enderror" required><option value="">Pilih jenis tanaman</option>@foreach($plantTypeOptions as $type)<option value="{{ $type }}" @selected(old('plant_type') === $type)>{{ $type }}</option>@endforeach</select>@error('plant_type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="tree_count">Jumlah pohon <span class="text-danger">*</span></label><input type="number" id="tree_count" name="tree_count" value="{{ old('tree_count') }}" class="form-control @error('tree_count') is-invalid @enderror" min="1" max="10000" placeholder="Contoh: 25" required>@error('tree_count')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="location_name">Nama lokasi</label><input id="location_name" name="location_name" value="{{ old('location_name', $accessToken->location_name) }}" class="form-control" placeholder="Contoh: Desa Adat Sanur"></div>
                </div>
                <div class="section-heading"><b>3</b>Titik lokasi</div><div class="map mb-2" id="planting-map"></div><div class="map-help mb-3"><i class="bi bi-info-circle me-1"></i>Klik peta untuk menaruh pin, atau gunakan lokasi perangkat Anda. Pastikan pin berada di wilayah Provinsi Bali.</div>
                <div class="row g-3 align-items-end"><div class="col-6"><label class="form-label" for="latitude">Latitude <span class="text-danger">*</span></label><input id="latitude" name="latitude" value="{{ old('latitude', $accessToken->latitude) }}" class="form-control @error('latitude') is-invalid @enderror" readonly required>@error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-6"><label class="form-label" for="longitude">Longitude <span class="text-danger">*</span></label><input id="longitude" name="longitude" value="{{ old('longitude', $accessToken->longitude) }}" class="form-control @error('longitude') is-invalid @enderror" readonly required>@error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-12"><button type="button" id="locate" class="btn btn-outline-success btn-sm"><i class="bi bi-crosshair me-1"></i>Gunakan lokasi saya</button> <span id="location-status" class="location-status ms-2"></span></div></div>
                <div class="section-heading"><b>4</b>Dokumentasi</div><label class="form-label" for="photo">Foto tanaman <span class="text-danger">*</span></label><label class="upload-box" for="photo"><i class="bi bi-cloud-arrow-up"></i><strong>Pilih atau seret foto ke sini</strong><small>JPG, PNG, atau WEBP · maksimal 5 MB</small><img id="photo-preview" class="preview d-none" alt="Pratinjau foto dokumentasi"></label><input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" class="d-none @error('photo') is-invalid @enderror" required>@error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <hr class="my-4"><div class="form-check"><input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox" value="1" id="consent" name="consent" @checked(old('consent')) required><label class="form-check-label small" for="consent">Saya menyatakan data yang diisi benar dan menyetujui penggunaannya untuk dokumentasi program.</label></div>@error('consent')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-footer"><button type="submit" id="submit-contribution" class="btn-submit" disabled><i class="bi bi-send me-1"></i>Simpan kontribusi</button><span class="form-text ms-2">Centang pernyataan di atas untuk mengaktifkan tombol simpan.</span></div>
        </form>
        <aside class="side-card"><span class="side-badge"><i class="bi bi-stars me-1"></i>Siap berkontribusi?</span><h3>Lengkapi data dengan mudah</h3><p>Siapkan beberapa informasi berikut sebelum mulai mengisi.</p><ul class="check-list"><li><i class="bi bi-check-circle-fill"></i><span>Identitas dasar dan instansi atau komunitas</span></li><li><i class="bi bi-check-circle-fill"></i><span>Jenis serta jumlah pohon yang ditanam</span></li><li><i class="bi bi-check-circle-fill"></i><span>Titik lokasi dari GPS atau peta</span></li><li><i class="bi bi-check-circle-fill"></i><span>Satu foto dokumentasi tanaman</span></li></ul><div class="impact"><i class="bi bi-tree-fill"></i><div><small>Total pohon dalam kontribusi ini</small><strong><span id="tree-count-summary">0</span> pohon</strong></div></div><div class="map-help mt-3"><i class="bi bi-shield-lock me-1"></i>Token ini hanya memberi izin mencatat data, bukan melihat data peserta lain.</div></aside>
    </div>
</main>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = document.getElementById('latitude'); const lng = document.getElementById('longitude'); const status = document.getElementById('location-status');
    const map = L.map('planting-map').setView([lat.value || -8.4095, lng.value || 115.1889], lat.value && lng.value ? 14 : 9); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map); let marker;
    const setLocation = (point) => { lat.value = point.lat.toFixed(7); lng.value = point.lng.toFixed(7); marker ? marker.setLatLng(point) : marker = L.marker(point).addTo(map); status.textContent = 'Lokasi berhasil dipilih'; };
    if (lat.value && lng.value) { marker = L.marker([lat.value, lng.value]).addTo(map); } map.on('click', (event) => setLocation(event.latlng));
    document.getElementById('locate').addEventListener('click', () => { if (!navigator.geolocation) { status.textContent = 'GPS tidak tersedia di perangkat ini'; return; } status.textContent = 'Mencari lokasi...'; navigator.geolocation.getCurrentPosition((position) => { const point = { lat: position.coords.latitude, lng: position.coords.longitude }; map.setView(point, 15); setLocation(point); }, () => { status.textContent = 'Lokasi tidak dapat diakses. Periksa izin GPS.'; }); });
    document.getElementById('photo').addEventListener('change', (event) => { const file = event.target.files[0]; const preview = document.getElementById('photo-preview'); if (file) { preview.src = URL.createObjectURL(file); preview.classList.remove('d-none'); } });
    const treeCount = document.getElementById('tree_count'); const summary = document.getElementById('tree-count-summary'); const updateSummary = () => { summary.textContent = Number(treeCount.value || 0).toLocaleString('id-ID'); }; treeCount.addEventListener('input', updateSummary); updateSummary();
    const consent = document.getElementById('consent'); const submitContribution = document.getElementById('submit-contribution'); const updateSubmitState = () => { submitContribution.disabled = !consent.checked; }; consent.addEventListener('change', updateSubmitState); updateSubmitState();
});
</script>
</body>
</html>
