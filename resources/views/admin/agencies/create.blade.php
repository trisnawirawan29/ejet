@extends('adminlte::page')
@section('title','Tambah Instansi')
@section('content_header')<h3 class="mb-0">Tambah instansi</h3>@stop
@section('content')<x-adminlte-card title="Informasi instansi" icon="bi bi-building" theme="success"><form method="POST" action="{{ route('admin.agencies.store') }}">@include('admin.catalog-form',['kind'=>'agency'])<button class="btn btn-success">Simpan</button> <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary">Batal</a></form></x-adminlte-card>@stop
