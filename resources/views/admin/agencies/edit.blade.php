@extends('adminlte::page')
@section('title','Edit Instansi')
@section('content_header')<h3 class="mb-0">Edit instansi</h3>@stop
@section('content')<x-adminlte-card title="Perbarui informasi instansi" icon="bi bi-pencil" theme="primary"><form method="POST" action="{{ route('admin.agencies.update',$agency) }}">@method('PUT') @include('admin.catalog-form',['kind'=>'agency','item'=>$agency])<button class="btn btn-primary">Simpan perubahan</button> <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary">Batal</a></form></x-adminlte-card>@stop
