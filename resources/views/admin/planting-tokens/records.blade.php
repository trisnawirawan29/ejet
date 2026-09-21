@extends('adminlte::page')

@section('title', 'Data Penanaman')

@push('css')
    <style>
        .records-card { border: 1px solid #e5eee8; border-radius: 16px; box-shadow: 0 10px 28px rgba(23, 70, 50, .06); }
        .records-card .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #edf2ee; background: #fbfdfb; }
        .records-card .card-body { padding: 1.5rem; }
        .records-meta { color: #718096; font-size: .78rem; }
        .records-table th { color: #547064; font-size: .72rem; letter-spacing: .03em; text-transform: uppercase; white-space: nowrap; }
        .records-table td { font-size: .8rem; }
        .records-table .participant-name { color: #244c3d; font-weight: 700; }
        .summary-card { height: 100%; border: 1px solid #e5eee8; border-radius: 14px; background: #fff; box-shadow: 0 6px 18px rgba(23, 70, 50, .045); }
        .summary-card__body { display: flex; align-items: center; gap: .8rem; padding: 1rem 1.1rem; }
        .summary-card__icon { display: inline-flex; width: 38px; height: 38px; flex: 0 0 38px; align-items: center; justify-content: center; border-radius: 11px; color: #23825f; background: #e5f5ec; }
        .summary-card__label { color: #718096; font-size: .7rem; }
        .summary-card__value { margin-top: .15rem; color: #244c3d; font-size: 1.35rem; font-weight: 800; line-height: 1; }
        .chart-card { height: 100%; border: 1px solid #e5eee8; border-radius: 16px; background: #fff; box-shadow: 0 6px 18px rgba(23, 70, 50, .045); }
        .chart-card__header { padding: 1.1rem 1.25rem .7rem; }
        .chart-card__title { margin: 0; color: #244c3d; font-size: .9rem; font-weight: 800; }
        .chart-card__subtitle { margin: .25rem 0 0; color: #718096; font-size: .72rem; }
        .bar-chart { display: grid; gap: .85rem; padding: .8rem 1.25rem 1.25rem; }
        .bar-chart__label { display: flex; justify-content: space-between; gap: 1rem; margin-bottom: .3rem; color: #547064; font-size: .73rem; }
        .bar-chart__label strong { color: #244c3d; }
        .bar-chart__track { height: 8px; overflow: hidden; border-radius: 99px; background: #eaf2ed; }
        .bar-chart__fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, #23825f, #72c39a); }
    </style>
@endpush

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h3 class="mb-1">Data penanaman</h3>
            <span class="records-meta">{{ $token->label }} · {{ $token->location_name }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.planting-tokens.show', $token) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-qr-code me-1"></i>QR Code</a>
            <a href="{{ route('admin.planting-tokens.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row g-3 mb-4">
        @foreach ([['label' => 'Total entri', 'value' => $summary['entries'], 'icon' => 'bi-list-check'], ['label' => 'Total pohon', 'value' => number_format($summary['trees'], 0, ',', '.'), 'icon' => 'bi-tree-fill'], ['label' => 'Peserta', 'value' => $summary['participants'], 'icon' => 'bi-people-fill'], ['label' => 'Jenis tanaman', 'value' => $summary['plantTypes'], 'icon' => 'bi-grid-3x3-gap-fill']] as $item)
            <div class="col-6 col-xl-3">
                <div class="summary-card"><div class="summary-card__body"><span class="summary-card__icon"><i class="bi {{ $item['icon'] }}"></i></span><div><div class="summary-card__label">{{ $item['label'] }}</div><div class="summary-card__value">{{ $item['value'] }}</div></div></div></div>
            </div>
        @endforeach
    </div>

    <div class="chart-card mb-4">
        <div class="chart-card__header"><h4 class="chart-card__title">Distribusi jenis pohon</h4><p class="chart-card__subtitle">Total pohon berdasarkan jenis tanaman pada token ini</p></div>
        @if ($plantTypeSummary->isNotEmpty())
            @php($maxPlantTypeTrees = max(1, $plantTypeSummary->max()))
            <div class="bar-chart" aria-label="Barchart distribusi jenis pohon">
                @foreach ($plantTypeSummary as $plantType => $treeCount)
                    <div>
                        <div class="bar-chart__label"><span>{{ $plantType }}</span><strong>{{ number_format($treeCount, 0, ',', '.') }} pohon</strong></div>
                        <div class="bar-chart__track"><div class="bar-chart__fill" style="width: {{ ($treeCount / $maxPlantTypeTrees) * 100 }}%"></div></div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small px-4 pb-4 mb-0">Belum ada data jenis pohon untuk ditampilkan.</p>
        @endif
    </div>

    <x-adminlte-card class="records-card" title="Daftar kontribusi masuk" icon="bi bi-tree" theme="success">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <div class="flex-grow-1"><input id="recordSearch" type="search" class="form-control" placeholder="Cari nama, email, instansi, atau jenis tanaman..." aria-label="Cari data penanaman"></div>
            <select id="recordPageSize" class="form-select" style="max-width: 180px" aria-label="Jumlah data per halaman"><option value="10">10 per halaman</option><option value="25">25 per halaman</option><option value="50">50 per halaman</option></select>
        </div>
        <div class="table-responsive">
            <table id="recordTable" class="records-table table table-hover align-middle mb-0">
                <thead><tr><th data-sort="name">Peserta <i class="bi bi-arrow-down-up small"></i></th><th data-sort="planted">Tanggal <i class="bi bi-arrow-down-up small"></i></th><th data-sort="plant">Tanaman <i class="bi bi-arrow-down-up small"></i></th><th data-sort="count">Jumlah <i class="bi bi-arrow-down-up small"></i></th><th data-sort="organization">Instansi / komunitas <i class="bi bi-arrow-down-up small"></i></th><th>Lokasi</th></tr></thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr data-row="{{ strtolower($record->name.' '.$record->email.' '.$record->organization.' '.$record->plant_type.' '.$record->location_name) }}" data-name="{{ strtolower($record->name) }}" data-planted="{{ $record->planted_at->timestamp }}" data-plant="{{ strtolower($record->plant_type) }}" data-count="{{ $record->tree_count }}" data-organization="{{ strtolower($record->organization ?? '') }}">
                            <td><span class="participant-name">{{ $record->name }}</span><div class="small text-muted">{{ $record->email ?: $record->phone }}</div></td>
                            <td>{{ $record->planted_at->format('d M Y') }}</td>
                            <td>{{ $record->plant_type }}</td>
                            <td><strong>{{ number_format($record->tree_count, 0, ',', '.') }}</strong> pohon</td>
                            <td>{{ $record->organization ?: '—' }}</td>
                            <td>{{ $record->location_name ?: '—' }}<div class="small text-muted">{{ $record->latitude }}, {{ $record->longitude }}</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data penanaman untuk token ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="recordTableFooter" class="d-flex justify-content-between align-items-center mt-3 small text-muted"></div>
    </x-adminlte-card>
@stop

@push('js')
    @include('admin.partials.datatable-script', ['tableId' => 'recordTable', 'searchId' => 'recordSearch', 'pageSizeId' => 'recordPageSize', 'footerId' => 'recordTableFooter'])
@endpush
