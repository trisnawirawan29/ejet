@extends('adminlte::page')
@section('title','Edit Jenis Tanaman')
@section('content_header')<h3 class="mb-0">Edit jenis tanaman</h3>@stop
@section('content')<x-adminlte-card title="Perbarui jenis tanaman" icon="bi bi-pencil" theme="primary"><form method="POST" action="{{ route('admin.plant-types.update',$plantType) }}">@method('PUT') @include('admin.catalog-form',['kind'=>'plant','item'=>$plantType])<button class="btn btn-primary">Simpan perubahan</button> <a href="{{ route('admin.plant-types.index') }}" class="btn btn-outline-secondary">Batal</a></form></x-adminlte-card>@stop
