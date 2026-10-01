<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Facility;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:menunggu,diverifikasi,diproses,selesai,ditolak'],
        ]);

        $complaints = Complaint::with(['user', 'category', 'facility'])
            ->when(! $user->isSuperAdmin() && ! $user->isGuru(), fn ($query) => $query->where('user_id', $user->id))
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('judul', 'like', '%'.$search.'%')
                        ->orWhere('nomor', 'like', '%'.$search.'%')
                        ->orWhere('lokasi', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('complaints.index', compact('complaints', 'filters'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()?->isUser(), 403);

        $categories = Category::all();
        $facilities = Facility::all();

        return view('user.complaints.create', compact('categories', 'facilities'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->isUser(), 403);

        $validated = $request->validate([
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'sarana_id' => ['nullable', 'exists:saranas,id'],
            'judul' => ['required', 'string', 'max:150'],
            'lokasi' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'menunggu';
        $validated['nomor'] = 'SP-'.Str::uuid();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('complaints', 'public');
        }

        $complaint = Complaint::create($validated);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Membuat pengaduan',
            'referensi' => $complaint->id,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'judul' => 'Pengaduan baru',
            'pesan' => 'Pengaduan Anda telah dikirim dan menunggu penanganan.',
        ]);

        User::whereIn('role', ['super_admin', 'guru'])
            ->where('status', 'aktif')
            ->each(function (User $staff) use ($complaint): void {
                Notification::create([
                    'user_id' => $staff->id,
                    'judul' => 'Pengaduan baru',
                    'pesan' => 'Pengaduan '.$complaint->nomor.' menunggu pemeriksaan.',
                ]);
            });

        return redirect()->route('user.complaints.index')->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function show(Complaint $complaint): View
    {
        $user = Auth::user();
        $canManageTechnician = $user?->isSuperAdmin() || $user?->isGuru();

        abort_unless(
            $canManageTechnician
                || $complaint->user_id === $user?->id
                || ($user?->isTechnician() && (int) $complaint->technician_id === (int) $user->id),
            404,
        );

        $complaint->load(['user', 'category', 'facility', 'technician', 'updates.user']);
        $technicians = $canManageTechnician
            ? User::query()->where('role', 'teknisi')->where('status', 'aktif')->orderBy('name')->get()
            : collect();
        $routePrefix = $user?->isGuru() ? 'guru' : 'admin';

        return view('complaints.show', compact('complaint', 'canManageTechnician', 'technicians', 'routePrefix'));
    }

    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_unless(Auth::user()?->isSuperAdmin() || Auth::user()?->isGuru(), 403);

        $request->validate([
            'status' => ['required', 'in:menunggu,diverifikasi,diproses,selesai,ditolak'],
            'description' => ['nullable', 'string'],
        ]);

        $complaint->update(['status' => $request->status]);
        $statusLabel = match ($request->status) {
            'menunggu' => 'Menunggu',
            'diverifikasi' => 'Diverifikasi',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        };

        $complaint->updates()->create([
            'user_id' => Auth::id(),
            'status' => $request->status,
            'isi' => $request->input('description') ?: 'Status pengaduan diubah menjadi '.$statusLabel.'.',
        ]);

        Notification::create([
            'user_id' => $complaint->user_id,
            'judul' => 'Status pengaduan diperbarui',
            'pesan' => 'Pengaduan '.$complaint->nomor.' sekarang berstatus '.$statusLabel.'.',
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Mengubah status pengaduan',
            'referensi' => $complaint->id,
        ]);

        return back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function storeResponse(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_unless(Auth::user()?->isSuperAdmin() || Auth::user()?->isGuru(), 403);

        $request->validate([
            'isi' => ['required', 'string'],
        ]);

        $complaint->updates()->create([
            'user_id' => Auth::id(),
            'status' => $complaint->status,
            'isi' => $request->isi,
        ]);

        $complaint->update(['response' => $request->isi]);

        Notification::create([
            'user_id' => $complaint->user_id,
            'judul' => 'Tanggapan baru',
            'pesan' => $request->isi,
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Mengirim tanggapan pengaduan',
            'referensi' => $complaint->id,
        ]);

        return back()->with('success', 'Tanggapan berhasil dikirim.');
    }

    public function destroy(Complaint $complaint): RedirectResponse
    {
        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menghapus pengaduan',
            'referensi' => $complaint->id,
        ]);

        $complaint->delete();

        return back()->with('success', 'Pengaduan berhasil dihapus.');
    }
}
