@extends('layouts.user')

@section('title', 'Buat Pengaduan')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header">
        <h4 class="mb-0">Form Pengaduan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('user.complaints.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-select" required>
                        <option value="rendah">Rendah</option>
                        <option value="sedang" selected>Sedang</option>
                        <option value="tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sarana</label>
                    <select name="sarana_id" class="form-select">
                        <option value="">Pilih sarana (opsional)</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-4">Kirim Pengaduan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
