@extends('adminlte::page')

@section('title', 'Token Penanaman')

@push('css')
<style>
    .gm-admin-modal .modal-content { overflow: hidden; border: 0; border-radius: 20px; box-shadow: 0 22px 60px rgba(23, 70, 50, .18); }
    .gm-admin-modal .modal-header { padding: 1.35rem 1.5rem; border-bottom: 1px solid #e8efeb; background: #fbfdfb; }
    .gm-admin-modal .modal-body { padding: 1.5rem; }
    .gm-admin-modal .modal-footer { padding: 1rem 1.5rem 1.35rem; border-top: 0; background: #fff; }
    .gm-admin-modal .form-label { margin-bottom: .45rem; color: #365348; font-size: .78rem; font-weight: 700; }
    .gm-admin-modal .form-control { min-height: 43px; border-color: #dce8df; border-radius: 10px; }
    .gm-admin-modal .form-control:focus { border-color: #6db895; box-shadow: 0 0 0 .2rem rgba(45, 155, 112, .12); }
    .gm-admin-modal .row { row-gap: 1rem !important; }
    .gm-admin-modal hr { margin: 1.25rem 0 0; border-color: #edf2ee; }
</style>
@endpush

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div><h3 class="mb-0">Token penanaman</h3><span class="text-muted small">Kelola akses publik dan QR Code kegiatan</span></div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTokenModal"><i class="bi bi-plus-lg me-1"></i>Buat token</button>
</div>
@stop

@section('content')
@foreach (['success' => 'success', 'error' => 'danger'] as $key => $theme)
    @if (session($key))
        <div class="alert alert-{{ $theme }}">{{ session($key) }}</div>
    @endif
@endforeach

@if (session('created_token'))
    <div class="card border-success mb-3"><div class="card-body d-flex flex-wrap align-items-center gap-3"><div class="flex-grow-1"><strong class="text-success"><i class="bi bi-check-circle me-1"></i>Token berhasil dibuat</strong><div class="small text-muted mt-1">Token hanya ditampilkan sekali. Simpan atau cetak QR Code sekarang.</div><code class="d-block mt-2">{{ session('created_token.value') }}</code></div><div id="token-qr" class="bg-white p-2"></div><a target="_blank" href="{{ session('created_token.url') }}" class="btn btn-outline-success btn-sm">Uji form</a></div></div>
@endif

<x-adminlte-card title="Daftar token akses" icon="bi bi-qr-code" theme="success">
    <div class="row g-2 mb-3"><div class="col-md-6"><label class="visually-hidden" for="tokenSearch">Cari token</label><input id="tokenSearch" type="search" class="form-control" placeholder="Cari label atau lokasi..."></div><div class="col-md-3"><label class="visually-hidden" for="tokenPageSize">Jumlah data</label><select id="tokenPageSize" class="form-select"><option value="10">10 per halaman</option><option value="25">25 per halaman</option></select></div></div>
    <div class="table-responsive"><table id="tokenTable" class="table table-hover align-middle mb-0"><thead><tr><th data-sort="label">Label <i class="bi bi-arrow-down-up small"></i></th><th data-sort="location">Lokasi default <i class="bi bi-arrow-down-up small"></i></th><th>Status</th><th data-sort="count">Data masuk <i class="bi bi-arrow-down-up small"></i></th><th class="text-end">Aksi</th></tr></thead><tbody>
    @forelse ($tokens as $token)
        <tr data-row="{{ strtolower($token->label.' '.$token->location_name) }}" data-label="{{ $token->label }}" data-location="{{ $token->location_name }}" data-count="{{ $token->planting_records_count }}"><td><strong>{{ $token->label }}</strong><div class="small text-muted">Dibuat {{ $token->created_at->format('d M Y') }}</div></td><td>{{ $token->location_name }}<div class="small text-muted">{{ $token->latitude }}, {{ $token->longitude }}</div></td><td>
            @if ($token->is_active && (!$token->expires_at || $token->expires_at->isFuture()))<span class="badge text-bg-success">Aktif</span>@else<span class="badge text-bg-secondary">Tidak aktif</span>@endif
            @if ($token->expires_at)<div class="small text-muted mt-1">s.d. {{ $token->expires_at->format('d M Y') }}</div>@endif
        </td><td>{{ $token->planting_records_count }} penanaman</td><td class="text-end text-nowrap"><a href="{{ route('admin.planting-tokens.records', $token) }}" class="btn btn-sm btn-outline-success" title="Lihat data masuk"><i class="bi bi-list-ul"></i><span class="visually-hidden">Lihat data masuk</span></a> <a href="{{ route('admin.planting-tokens.show', $token) }}" class="btn btn-sm btn-outline-success" title="Lihat QR Code"><i class="bi bi-qr-code"></i><span class="visually-hidden">Lihat QR Code</span></a> <a href="{{ route('admin.planting-tokens.edit', $token) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i><span class="visually-hidden">Edit</span></a><form class="d-inline" method="POST" action="{{ route('admin.planting-tokens.destroy', $token) }}" onsubmit="return confirm('Hapus token ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i><span class="visually-hidden">Hapus</span></button></form></td></tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada token.</td></tr>
    @endforelse
    </tbody></table></div>
</x-adminlte-card>

<div class="modal fade gm-admin-modal" id="createTokenModal" tabindex="-1" aria-labelledby="createTokenModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="createTokenModalLabel"><i class="bi bi-qr-code text-success me-2"></i>Buat token penanaman</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><form method="POST" action="{{ route('admin.planting-tokens.store') }}"><div class="modal-body">@include('admin.planting-tokens.form')</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Buat token</button></div></form></div></div></div>
@stop

@push('js')
@if (session('created_token'))
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>document.addEventListener('DOMContentLoaded',()=>{const qr=document.getElementById('token-qr');if(qr){new QRCode(qr,{text:@json(session('created_token.url')),width:112,height:112,colorDark:'#176246',colorLight:'#ffffff'});}});</script>
@endif
@endpush

@push('js')
@include('admin.partials.datatable-script', ['tableId' => 'tokenTable', 'searchId' => 'tokenSearch', 'pageSizeId' => 'tokenPageSize', 'footerId' => 'tokenTableFooter'])
<script>document.addEventListener('DOMContentLoaded',()=>{const table=document.getElementById('tokenTable');if(table){const footer=document.createElement('div');footer.id='tokenTableFooter';footer.className='d-flex justify-content-between align-items-center mt-3 small text-muted';table.parentElement.append(footer)}});</script>
@endpush
