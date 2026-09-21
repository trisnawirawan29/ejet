@extends('adminlte::page')

@section('title', $applicationName)

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root { --forest: #176246; --ink: #173129; --muted: #71837c; --line: #e2ebe6; --canvas: #f4f8f5; }
        .content-wrapper { background: var(--canvas); }
        .dashboard-shell { color: var(--ink); }
        .dashboard-shell .card, .dashboard-hero { border: 1px solid var(--line); border-radius: 18px; background: #fff; box-shadow: 0 8px 26px rgba(23, 70, 50, .055); }
        .dashboard-shell .card-header { padding: 1.1rem 1.3rem; border-bottom: 1px solid var(--line); background: transparent; }
        .dashboard-shell .card-body { padding: 1.3rem; }
        .dashboard-hero { position: relative; overflow: hidden; padding: 1.7rem 2rem; color: #fff; background: linear-gradient(115deg, #0c3c2d, #176246 58%, #2c8966); }
        .dashboard-hero::after { position: absolute; right: -80px; bottom: -180px; width: 360px; height: 360px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; content: ''; }
        .dashboard-hero > * { position: relative; z-index: 1; }
        .dashboard-hero__eyebrow { color: #8bd2ad; font-size: .68rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
        .dashboard-hero h1 { max-width: 650px; margin: .55rem 0 .4rem; font-size: clamp(1.55rem, 3vw, 2.25rem); letter-spacing: -.04em; }
        .dashboard-hero p { max-width: 650px; margin: 0; color: #d2e8db; font-size: .9rem; }
        .dashboard-hero__date { display: block; margin-top: 1rem; color: #d2e8db; font-size: .75rem; }
        .kpi { height: 100%; padding: 1.1rem 1.15rem; }
        .kpi__icon { display: inline-flex; align-items: center; justify-content: center; width: 39px; height: 39px; border-radius: 12px; font-size: 1rem; }
        .kpi__label { margin-top: 1rem; color: var(--muted); font-size: .75rem; }
        .kpi__value { margin: .25rem 0 .3rem; font-size: 1.65rem; font-weight: 800; letter-spacing: -.05em; }
        .kpi__caption { color: #9baaa3; font-size: .7rem; }
        .tone-green { color: #21825d; background: #e5f5ec; } .tone-blue { color: #3978b7; background: #e8f2fc; } .tone-orange { color: #c67b2d; background: #fff1e2; } .tone-purple { color: #855db0; background: #f1eafb; }
        .section-title { margin: 0; font-size: .94rem; font-weight: 800; }
        .section-subtitle { margin: .2rem 0 0; color: var(--muted); font-size: .71rem; }
        .monthly-chart { display: grid; grid-template-columns: repeat(12, minmax(28px, 1fr)); align-items: end; gap: .6rem; height: 225px; padding-top: 1rem; }
        .monthly-chart__item { display: flex; height: 100%; flex-direction: column; align-items: center; justify-content: end; gap: .45rem; }
        .monthly-chart__bar-wrap { display: flex; width: 100%; height: 180px; align-items: end; }
        .monthly-chart__bar { width: 100%; min-height: 3px; border-radius: 7px 7px 2px 2px; background: linear-gradient(180deg, #64c394, #23825f); }
        .monthly-chart__value { color: var(--muted); font-size: .6rem; }
        .monthly-chart__month { color: #97a69f; font-size: .65rem; }
        .map-panel { height: 350px; overflow: hidden; border-radius: 14px; background: #e7f2ea; }
        #dashboard-map { width: 100%; height: 100%; }
        .map-empty { display: flex; height: 100%; align-items: center; justify-content: center; color: var(--muted); font-size: .8rem; text-align: center; }
        .location-row, .agency-row, .activity-row { display: flex; align-items: center; gap: .75rem; padding: .78rem 0; border-bottom: 1px solid var(--line); }
        .location-row:last-child, .agency-row:last-child, .activity-row:last-child { border-bottom: 0; }
        .row-dot { width: 8px; height: 8px; flex: 0 0 8px; border-radius: 50%; background: #35a275; box-shadow: 0 0 0 4px #e4f3eb; }
        .row-main { min-width: 0; flex: 1; font-size: .75rem; font-weight: 700; }
        .row-main small { display: block; margin-top: .2rem; color: var(--muted); font-size: .66rem; font-weight: 400; }
        .row-meta { color: var(--muted); font-size: .66rem; text-align: right; white-space: nowrap; }
        .row-meta strong { display: block; color: var(--forest); font-size: .78rem; }
        .gm-agency__badge, .gm-activity__icon { display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; flex: 0 0 35px; border-radius: 11px; font-size: .8rem; }
        .gm-badge { padding: .3rem .5rem; border-radius: 20px; color: #237b5c; background: #e5f5ec; font-size: .6rem; font-weight: 700; white-space: nowrap; }
        .empty-state { padding: 2rem 1rem; color: var(--muted); font-size: .78rem; text-align: center; }
        @media (max-width: 900px) { .monthly-chart { gap: .3rem; } }
        @media (max-width: 575px) { .dashboard-hero { padding: 1.4rem; } .monthly-chart { gap: .15rem; } .monthly-chart__value { display: none; } }
    </style>
@endpush

@section('content_header')
    <div class="d-flex align-items-center justify-content-between"><div><h3 class="mb-0">Dashboard</h3><span class="text-muted small">Ringkasan data penanaman aplikasi</span></div><span class="badge rounded-pill text-bg-light border px-3 py-2"><i class="bi bi-circle-fill text-success me-1" style="font-size:.45rem;vertical-align:middle"></i> Data terbaru</span></div>
@stop

@section('content')
<div class="dashboard-shell">
    <section class="dashboard-hero mb-4"><span class="dashboard-hero__eyebrow">{{ $applicationName }}</span><h1>{{ $applicationTagline }}</h1><p>Ringkasan ini menampilkan data penanaman yang benar-benar tercatat di aplikasi.</p><span class="dashboard-hero__date"><i class="bi bi-calendar3 me-1"></i> Tahun {{ $dashboard['year'] }}</span></section>

    <div class="row g-3 mb-4">
        @foreach ($dashboard['summary'] as $stat)
            <div class="col-6 col-xl-3"><div class="kpi card"><span class="kpi__icon tone-{{ $stat['tone'] }}"><i class="bi {{ $stat['icon'] }}"></i></span><div class="kpi__label">{{ $stat['label'] }}</div><div class="kpi__value">{{ $stat['value'] }}</div><span class="kpi__caption">{{ $stat['caption'] }}</span></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8"><div class="card h-100"><div class="card-header"><h2 class="section-title">Pertumbuhan penanaman</h2><p class="section-subtitle">Jumlah pohon berdasarkan bulan penanaman tahun {{ $dashboard['year'] }}</p></div><div class="card-body"><div class="monthly-chart" aria-label="Grafik pertumbuhan penanaman per bulan">@php($maxMonthly = max(1, collect($dashboard['monthly'])->max('value'))) @foreach ($dashboard['monthly'] as $month)<div class="monthly-chart__item"><span class="monthly-chart__value">{{ $month['value'] ? number_format($month['value'], 0, ',', '.') : '0' }}</span><div class="monthly-chart__bar-wrap"><div class="monthly-chart__bar" style="height: {{ max(2, ($month['value'] / $maxMonthly) * 100) }}%" title="{{ $month['month'] }}: {{ number_format($month['value'], 0, ',', '.') }} pohon"></div></div><span class="monthly-chart__month">{{ $month['month'] }}</span></div>@endforeach</div></div></div></div>
        <div class="col-xl-4"><div class="card h-100"><div class="card-header"><h2 class="section-title">Ringkasan periode</h2><p class="section-subtitle">Data aktual yang tercatat</p></div><div class="card-body d-flex flex-column justify-content-center"><div class="d-flex align-items-end gap-2 mb-2"><span style="font-size:2.5rem;font-weight:800;letter-spacing:-.07em;color:var(--forest)">{{ number_format($dashboard['totalTrees'], 0, ',', '.') }}</span><span class="text-muted small mb-2">pohon</span></div><p class="text-muted small mb-0">Tidak ada target tahunan yang dikonfigurasi. Angka di atas adalah akumulasi seluruh data penanaman.</p></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-7"><div class="card h-100"><div class="card-header"><h2 class="section-title">Peta lokasi penanaman</h2><p class="section-subtitle">Lokasi yang tercatat dari data penanaman</p></div><div class="card-body"><div class="map-panel">@if (count($dashboard['locations']))<div id="dashboard-map" aria-label="Peta lokasi penanaman"></div>@else<div class="map-empty"><div><i class="bi bi-geo-alt fs-3 d-block mb-2"></i>Belum ada lokasi penanaman.</div></div>@endif</div></div></div></div>
        <div class="col-xl-5"><div class="card h-100"><div class="card-header"><h2 class="section-title">Lokasi teratas</h2><p class="section-subtitle">Berdasarkan jumlah pohon tercatat</p></div><div class="card-body py-2">@forelse ($dashboard['locations'] as $location)<div class="location-row"><span class="row-dot"></span><span class="row-main">{{ $location['name'] }}<small>{{ $location['records'] }} entri</small></span><span class="row-meta"><strong>{{ number_format($location['trees'], 0, ',', '.') }}</strong> pohon</span></div>@empty<div class="empty-state">Belum ada data lokasi.</div>@endforelse</div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-xl-7"><div class="card h-100"><div class="card-header"><h2 class="section-title">Instansi / komunitas</h2><p class="section-subtitle">Organisasi yang tercatat pada data peserta</p></div><div class="card-body py-2">@forelse ($dashboard['agencies'] as $agency)<div class="agency-row"><span class="gm-agency__badge tone-{{ $agency['tone'] }}"><i class="bi bi-building"></i></span><span class="row-main">{{ $agency['short_name'] }}<small>{{ $agency['people'] }} peserta</small></span><span class="row-meta"><strong>{{ number_format($agency['trees'], 0, ',', '.') }}</strong> pohon</span></div>@empty<div class="empty-state">Belum ada instansi atau komunitas pada data penanaman.</div>@endforelse</div></div></div>
        <div class="col-xl-5"><div class="card h-100"><div class="card-header"><h2 class="section-title">Aktivitas terbaru</h2><p class="section-subtitle">Data penanaman terakhir masuk</p></div><div class="card-body py-2">@forelse ($dashboard['activities'] as $activity)<div class="activity-row"><span class="gm-activity__icon tone-{{ $activity['tone'] }}"><i class="bi bi-tree-fill"></i></span><span class="row-main">{{ $activity['title'] }}<small>{{ $activity['meta'] }}</small></span><span class="gm-badge green">{{ $activity['status'] }}</span></div>@empty<div class="empty-state">Belum ada aktivitas penanaman.</div>@endforelse</div></div></div>
    </div>

    @if ($userStatistics)
        <div class="card mt-3"><div class="card-body py-3"><div class="d-flex flex-wrap align-items-center gap-3"><strong class="small me-auto"><i class="bi bi-shield-check text-success me-1"></i> Ringkasan akses admin</strong><span class="text-muted small">{{ $userStatistics['total'] }} pengguna</span><span class="text-success small">Pengguna terverifikasi: {{ $userStatistics['verified'] }}</span><span class="text-warning small">Menunggu verifikasi: {{ $userStatistics['pending'] }}</span><span class="text-muted small">Pengguna nonaktif: {{ $userStatistics['inactive'] }}</span></div></div></div>
    @endif
</div>
@stop

@push('js')
    @if (count($dashboard['locations']))
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const locations = @json($dashboard['locations']);
                const map = L.map('dashboard-map', { scrollWheelZoom: false });
                const bounds = L.latLngBounds(locations.map(location => [location.latitude, location.longitude]));
                map.fitBounds(bounds, { padding: [24, 24], maxZoom: 13 });
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors', maxZoom: 18 }).addTo(map);
                locations.forEach(location => L.marker([location.latitude, location.longitude]).addTo(map).bindPopup(`<strong>${location.name}</strong><br>${Number(location.trees).toLocaleString('id-ID')} pohon · ${location.records} entri`));
            });
        </script>
    @endif
@endpush
