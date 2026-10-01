<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(): View
    {
        $technicians = User::query()
            ->where('role', 'teknisi')
            ->withCount('assignedComplaints')
            ->orderBy('name')
            ->get();
        $isGuru = Auth::user()?->isGuru() ?? false;
        $layout = $isGuru ? 'layouts.guru' : 'layouts.admin';
        $routePrefix = $isGuru ? 'guru' : 'admin';

        return view('technicians.index', compact('technicians', 'layout', 'routePrefix'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $technician = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'position' => $validated['position'] ?? 'Teknisi Sarana dan Prasarana',
            'password' => Hash::make($validated['password']),
            'role' => 'teknisi',
            'status' => 'aktif',
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menambahkan teknisi: '.$technician->name,
            'referensi' => $technician->id,
        ]);

        return back()->with('success', 'Akun teknisi berhasil dibuat.');
    }

    public function dashboard(): View
    {
        $technician = Auth::user();
        $complaints = $technician->assignedComplaints()
            ->with(['user', 'category', 'facility'])
            ->latest()
            ->paginate(12);
        $stats = [
            'total' => $technician->assignedComplaints()->count(),
            'menunggu' => $technician->assignedComplaints()->whereIn('status', ['menunggu', 'diverifikasi'])->count(),
            'diproses' => $technician->assignedComplaints()->where('status', 'diproses')->count(),
            'selesai' => $technician->assignedComplaints()->where('status', 'selesai')->count(),
        ];

        return view('technicians.dashboard', compact('complaints', 'stats'));
    }

    public function assign(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_unless(Auth::user()?->isSuperAdmin() || Auth::user()?->isGuru(), 403);
        abort_unless(in_array($complaint->status, ['menunggu', 'diverifikasi', 'diproses'], true), 404);

        $validated = $request->validate([
            'technician_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'teknisi')->where('status', 'aktif')),
            ],
        ]);
        $technician = isset($validated['technician_id']) ? User::findOrFail($validated['technician_id']) : null;
        $assignmentMessage = $technician
            ? 'Pengaduan ditugaskan kepada teknisi '.$technician->name.'.'
            : 'Penugasan teknisi pengaduan dibatalkan.';

        DB::transaction(function () use ($complaint, $technician, $assignmentMessage): void {
            $complaint->update(['technician_id' => $technician?->id]);
            $complaint->updates()->create([
                'user_id' => Auth::id(),
                'status' => $complaint->status,
                'isi' => $assignmentMessage,
            ]);

            if ($technician) {
                Notification::create([
                    'user_id' => $technician->id,
                    'judul' => 'Tugas perbaikan baru',
                    'pesan' => 'Pengaduan '.$complaint->nomor.' ditugaskan kepada Anda.',
                ]);
            }

            Activity::create([
                'user_id' => Auth::id(),
                'aktivitas' => $assignmentMessage,
                'referensi' => $complaint->id,
            ]);
        });

        return back()->with('success', 'Penugasan teknisi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_unless((int) $complaint->technician_id === (int) Auth::id(), 404);
        abort_unless(in_array($complaint->status, ['menunggu', 'diverifikasi', 'diproses'], true), 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['diproses', 'selesai'])],
            'isi' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($complaint, $validated): void {
            $complaint->update(['status' => $validated['status']]);
            $complaint->updates()->create([
                'user_id' => Auth::id(),
                'status' => $validated['status'],
                'isi' => $validated['isi'],
            ]);
            Notification::create([
                'user_id' => $complaint->user_id,
                'judul' => 'Progres perbaikan diperbarui',
                'pesan' => 'Tugas perbaikan '.$complaint->nomor.' diperbarui: '.($validated['status'] === 'selesai' ? 'Selesai' : 'Sedang diproses').'.',
            ]);
            Activity::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Memperbarui progres perbaikan',
                'referensi' => $complaint->id,
            ]);
        });

        return back()->with('success', 'Progres perbaikan berhasil diperbarui.');
    }
}
