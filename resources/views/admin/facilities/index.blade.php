@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.guru')

@section('title', 'Sarana & Prasarana')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Sarana & Prasarana</h4>
        @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary btn-sm">Tambah Sarana</a>
        @endif
    </div>
    <form method="GET" action="{{ route('admin.facilities.index') }}" class="row g-2 p-3 border-bottom">
        <div class="col-lg-5"><label for="facility-search" class="visually-hidden">Cari sarana</label><input id="facility-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, kode, lokasi"></div>
        <div class="col-lg-3"><label for="facility-condition" class="visually-hidden">Kondisi</label><select id="facility-condition" name="kondisi" class="form-select"><option value="">Semua kondisi</option>@foreach (['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat'] as $value => $label)<option value="{{ $value }}" @selected(($filters['kondisi'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-lg-2"><label for="facility-status" class="visually-hidden">Status</label><select id="facility-status" name="status" class="form-select"><option value="">Semua status</option><option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option><option value="nonaktif" @selected(($filters['status'] ?? '') === 'nonaktif')>Nonaktif</option></select></div>
        <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Terapkan</button><a class="btn btn-outline-secondary" href="{{ route('admin.facilities.index') }}">Reset</a></div>
    </form>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facilities as $facility)
                        <tr>
                            <td>{{ $facility->nama }}</td>
                            <td>{{ $facility->kode }}</td>
                            <td>{{ $facility->lokasi }}</td>
                            <td>{{ $facility->kondisi }}</td>
                            <td><div class="d-flex gap-2">
                                <a href="{{ route(auth()->user()->isGuru() ? 'guru.facilities.show' : 'admin.facilities.show', $facility) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                @if (auth()->user()->isSuperAdmin())
                                    <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Hapus sarana ini?')">Hapus</button>
                                    </form>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $facilities->links() }}</div>
    </div>
</div>
@endsection
