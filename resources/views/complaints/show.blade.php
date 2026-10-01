@extends(auth()->user()->role === 'guru' ? 'layouts.guru' : (auth()->user()->role === 'super_admin' ? 'layouts.admin' : (auth()->user()->isTechnician() ? 'layouts.technician' : 'layouts.user')))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Pengaduan</h4>
        <span class="badge bg-warning text-dark">{{ $complaint->status }}</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Nomor:</strong> {{ $complaint->nomor }}</div>
            <div class="col-md-6"><strong>Judul:</strong> {{ $complaint->judul }}</div>
            <div class="col-md-6"><strong>Pelapor:</strong> {{ $complaint->user->name ?? '-' }}</div>
            <div class="col-md-6"><strong>Kategori:</strong> {{ $complaint->category->nama ?? '-' }}</div>
            <div class="col-md-6"><strong>Lokasi:</strong> {{ $complaint->lokasi }}</div>
            <div class="col-md-6"><strong>Prioritas:</strong> {{ $complaint->prioritas }}</div>
            <div class="col-12"><strong>Deskripsi:</strong><br>{{ $complaint->deskripsi }}</div>
            @if ($canManageTechnician)
                <div class="col-12">
                    <strong>Teknisi:</strong>
                    @if ($complaint->technician)
                        {{ $complaint->technician->name }}
                        @if ($complaint->technician->phone)
                            · <a href="tel:{{ $complaint->technician->phone }}">{{ $complaint->technician->phone }}</a>
                        @endif
                    @else
                        Belum ditugaskan
                    @endif
                    @if (in_array($complaint->status, ['menunggu', 'diverifikasi', 'diproses'], true))
                        <form action="{{ route($routePrefix.'.complaints.technician', $complaint) }}" method="POST" class="row g-2 mt-1 align-items-end">
                            @csrf
                            @method('PATCH')
                            <div class="col-md-8">
                                <label for="technician-assignment" class="form-label">Tugaskan teknisi</label>
                                <select id="technician-assignment" name="technician_id" class="form-select">
                                    <option value="">Belum ditugaskan</option>
                                    @foreach ($technicians as $technician)
                                        <option value="{{ $technician->id }}" @selected((int) $complaint->technician_id === (int) $technician->id)>{{ $technician->name }} · {{ $technician->position ?: 'Teknisi Sarana dan Prasarana' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><button class="btn btn-outline-primary" type="submit">Simpan penugasan</button></div>
                        </form>
                    @else
                        <div class="text-muted small mt-1">Penugasan tertutup untuk status ini.</div>
                    @endif
                </div>
            @elseif (Auth::user()->isTechnician())
                <div class="col-12"><strong>Penugasan:</strong> Tugas ini diberikan kepada Anda.</div>
            @endif
        </div>

        <section class="mt-4" aria-labelledby="responses-heading">
            <h5 id="responses-heading">Riwayat Tanggapan</h5>
            <div class="list-group">
                @forelse ($complaint->updates as $update)
                    <article class="list-group-item">
                        <div class="d-flex justify-content-between gap-3">
                            <strong>{{ $update->user->name ?? 'Sistem' }}</strong>
                            <time class="text-muted small" datetime="{{ $update->created_at->toIso8601String() }}">{{ $update->created_at->format('d M Y H:i') }}</time>
                        </div>
                        @if ($update->status)
                            <div class="small text-muted">Status: {{ $update->status }}</div>
                        @endif
                        <p class="mb-0 mt-2">{{ $update->isi }}</p>
                    </article>
                @empty
                    <div class="list-group-item text-muted">Belum ada tanggapan.</div>
                @endforelse
            </div>
        </section>

        @if (Auth::user()->isGuru() || Auth::user()->isSuperAdmin())
            <form action="{{ route('complaints.response', $complaint) }}" method="POST" class="mt-4">
                @csrf
                <label for="response-body" class="form-label">Tanggapan Pengaduan</label>
                <textarea id="response-body" name="isi" class="form-control" rows="3" maxlength="5000" required>{{ old('isi') }}</textarea>
                @error('isi')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn btn-outline-primary mt-2">Kirim Tanggapan</button>
            </form>
        @endif

        @if (Auth::user()->isGuru() || Auth::user()->isSuperAdmin())
            <form action="{{ route('complaints.updateStatus', $complaint) }}" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Ubah Status</label>
                        <select name="status" class="form-select">
                            <option value="menunggu" {{ $complaint->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="diverifikasi" {{ $complaint->status == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="diproses" {{ $complaint->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ $complaint->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ $complaint->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="description" class="form-control" placeholder="Catatan penanganan">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Simpan</button>
                    </div>
                </div>
            </form>
        @endif

        @if (Auth::user()->isSuperAdmin())
            <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" class="mt-3" onsubmit="return confirm('Hapus pengaduan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Hapus Pengaduan</button>
            </form>
        @endif
    </div>
</div>
@endsection
