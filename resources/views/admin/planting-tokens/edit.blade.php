@extends('adminlte::page')
@section('title', 'Edit Token Penanaman')
@section('content_header')<h3 class="mb-0">Edit token penanaman</h3>@stop
@section('content')<x-adminlte-card title="Perbarui detail akses" icon="bi bi-pencil" theme="primary"><form method="POST" action="{{ route('admin.planting-tokens.update',$token) }}">@method('PUT') @include('admin.planting-tokens.form', ['token' => $token])<button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan perubahan</button> <a href="{{ route('admin.planting-tokens.index') }}" class="btn btn-outline-secondary">Batal</a></form></x-adminlte-card>@stop
