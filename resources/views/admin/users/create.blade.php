@extends('adminlte::page')
@section('title', 'Tambah Pengguna')
@section('content_header')<h3>Tambah Pengguna</h3>@stop
@section('content')<x-adminlte-card title="Data pengguna" icon="bi bi-person-plus" theme="primary"><form action="{{ route('admin.users.store') }}" method="post">@include('admin.users._form')<div class="mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button> <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a></div></form></x-adminlte-card>@stop
