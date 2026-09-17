@extends('adminlte::page')
@section('title', 'Buat Token Penanaman')
@section('content_header')<h3 class="mb-0">Buat token penanaman</h3>@stop
@section('content')<x-adminlte-card title="Detail akses" icon="bi bi-qr-code" theme="success"><form method="POST" action="{{ route('admin.planting-tokens.store') }}">@include('admin.planting-tokens.form')<button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Buat token & QR Code</button> <a href="{{ route('admin.planting-tokens.index') }}" class="btn btn-outline-secondary">Batal</a></form></x-adminlte-card>@stop
