@extends('adminlte::page')
@section('title', 'Edit Pengguna')
@section('content_header')<h3>Edit Pengguna</h3>@stop
@section('content')<x-adminlte-card title="Data pengguna" icon="bi bi-pencil" theme="primary"><form action="{{ route('admin.users.update', $user) }}" method="post">@method('PUT')@include('admin.users._form', ['user' => $user])<div class="mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan perubahan</button> <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a></div></form></x-adminlte-card>@stop
