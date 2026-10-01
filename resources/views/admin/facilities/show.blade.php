@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.guru')

@section('title', 'Detail Sarana')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header"><h1 class="h4 mb-0">Detail Sarana & Prasarana</h1></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Nama</strong><div>{{ $facility->nama }}</div></div>
            <div class="col-md-6"><strong>Kode</strong><div>{{ $facility->kode }}</div></div>
            <div class="col-md-6"><strong>Kategori</strong><div>{{ $facility->category->nama ?? '-' }}</div></div>
            <div class="col-md-6"><strong>Lokasi</strong><div>{{ $facility->lokasi }}</div></div>
            <div class="col-md-4"><strong>Kondisi</strong><div>{{ $facility->kondisi }}</div></div>
            <div class="col-md-4"><strong>Jumlah</strong><div>{{ $facility->jumlah }}</div></div>
            <div class="col-md-4"><strong>Status</strong><div>{{ $facility->status }}</div></div>
            <div class="col-12"><strong>Keterangan</strong><div>{{ $facility->deskripsi ?? '-' }}</div></div>
            @if ($facility->foto)
                <div class="col-12"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($facility->foto) }}" alt="Foto {{ $facility->nama }}" class="img-fluid rounded" style="max-height: 320px"></div>
            @endif
        </div>
        <div class="d-flex gap-2 mt-4">
            @if (auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-primary">Edit Sarana</a>
                <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary">Kembali</a>
            @else
                <a href="{{ route('guru.facilities.index') }}" class="btn btn-outline-secondary">Kembali</a>
            @endif
        </div>
    </div>
</div>
@endsection
