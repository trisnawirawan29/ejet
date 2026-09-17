@extends('adminlte::page')
@section('title','Tambah Jenis Tanaman')
@section('content_header')<h3 class="mb-0">Tambah jenis tanaman</h3>@stop
@section('content')<x-adminlte-card title="Informasi jenis tanaman" icon="bi bi-tree" theme="success"><form method="POST" action="{{ route('admin.plant-types.store') }}">@include('admin.catalog-form',['kind'=>'plant'])<button class="btn btn-success">Simpan</button> <a href="{{ route('admin.plant-types.index') }}" class="btn btn-outline-secondary">Batal</a></form></x-adminlte-card>@stop
