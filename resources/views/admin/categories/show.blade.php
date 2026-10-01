@extends('layouts.admin')

@section('title', 'Detail Kategori')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header"><h1 class="h4 mb-0">Detail Kategori</h1></div>
    <div class="card-body">
        <dl class="row mb-4">
            <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">{{ $category->nama }}</dd>
            <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9">{{ $category->deskripsi ?? '-' }}</dd>
            <dt class="col-sm-3">Sarana terkait</dt><dd class="col-sm-9">{{ $category->facilities_count }}</dd>
            <dt class="col-sm-3">Pengaduan terkait</dt><dd class="col-sm-9">{{ $category->complaints_count }}</dd>
        </dl>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">Edit Kategori</a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>
@endsection
