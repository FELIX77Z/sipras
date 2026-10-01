<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $stats = [
            'totalGuru' => User::where('role', 'guru')->count(),
            'totalTeknisi' => User::where('role', 'teknisi')->count(),
            'totalSiswa' => User::whereIn('role', ['user', 'siswa'])->count(),
            'totalPengaduan' => Complaint::count(),
            'baru' => Complaint::where('status', 'menunggu')->count(),
            'diverifikasi' => Complaint::where('status', 'diverifikasi')->count(),
            'diproses' => Complaint::where('status', 'diproses')->count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('status', 'ditolak')->count(),
            'totalSarana' => Facility::count(),
        ];

        $complaints = Complaint::with(['user', 'category', 'facility'])->latest()->take(8)->get();
        $activities = Activity::with('user')->latest()->take(8)->get();
        $users = User::latest()->take(6)->get();

        $counts = (object) [
            'total' => Complaint::count(),
            'menunggu' => Complaint::where('status', 'menunggu')->count(),
            'diverifikasi' => Complaint::where('status', 'diverifikasi')->count(),
            'diproses' => Complaint::where('status', 'diproses')->count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('status', 'ditolak')->count(),
        ];

        $charts = $this->buildCharts(includeRoles: true);

        return view('admin.dashboard', compact('stats', 'complaints', 'activities', 'users', 'counts', 'charts'));
    }

    public function guru(): View
    {
        $user = Auth::user();

        $complaints = Complaint::with(['user', 'category', 'facility'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'totalTeknisi' => User::where('role', 'teknisi')->count(),
            'totalSiswa' => User::whereIn('role', ['user', 'siswa'])->count(),
            'totalPengaduan' => Complaint::count(),
            'baru' => Complaint::where('status', 'menunggu')->count(),
            'diverifikasi' => Complaint::where('status', 'diverifikasi')->count(),
            'diproses' => Complaint::where('status', 'diproses')->count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('status', 'ditolak')->count(),
        ];

        $charts = $this->buildCharts();

        return view('guru.dashboard', compact('stats', 'complaints', 'user', 'charts'));
    }

    public function user(): View
    {
        $user = Auth::user();
        $complaints = Complaint::where('user_id', $user->id)->latest()->take(6)->get();

        $counts = (object) [
            'total' => Complaint::where('user_id', $user->id)->count(),
            'menunggu' => Complaint::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'diverifikasi' => Complaint::where('user_id', $user->id)->where('status', 'diverifikasi')->count(),
            'diproses' => Complaint::where('user_id', $user->id)->where('status', 'diproses')->count(),
            'selesai' => Complaint::where('user_id', $user->id)->where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('user_id', $user->id)->where('status', 'ditolak')->count(),
        ];

        $stats = [
            'total' => Complaint::where('user_id', $user->id)->count(),
            'diajukan' => Complaint::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'diverifikasi' => Complaint::where('user_id', $user->id)->where('status', 'diverifikasi')->count(),
            'diproses' => Complaint::where('user_id', $user->id)->where('status', 'diproses')->count(),
            'selesai' => Complaint::where('user_id', $user->id)->where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('user_id', $user->id)->where('status', 'ditolak')->count(),
        ];

        $charts = $this->buildCharts($user);

        return view('user.dashboard', compact('stats', 'complaints', 'user', 'counts', 'charts'));
    }

    public function activities(): View
    {
        $activities = Activity::with('user')->latest()->paginate(12);

        return view('admin.activities', compact('activities'));
    }

    public function reports(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:menunggu,diverifikasi,diproses,selesai,ditolak'],
        ]);
        $query = Complaint::with(['user', 'category', 'facility'])
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(fn ($query) => $query->where('nomor', 'like', '%'.$search.'%')->orWhere('judul', 'like', '%'.$search.'%')->orWhere('lokasi', 'like', '%'.$search.'%')->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'))))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status));
        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');
        $summary = [
            'total' => (clone $query)->count(),
            'menunggu' => (int) ($statusCounts['menunggu'] ?? 0),
            'diverifikasi' => (int) ($statusCounts['diverifikasi'] ?? 0),
            'diproses' => (int) ($statusCounts['diproses'] ?? 0),
            'selesai' => (int) ($statusCounts['selesai'] ?? 0),
            'ditolak' => (int) ($statusCounts['ditolak'] ?? 0),
        ];
        $printMode = $request->boolean('print');
        $complaints = $printMode
            ? (clone $query)->latest()->get()
            : (clone $query)->latest()->paginate(20)->withQueryString();
        $isGuru = Auth::user()?->isGuru() ?? false;
        $layout = $isGuru ? 'layouts.guru' : 'layouts.admin';
        $reportRoute = $isGuru ? 'guru.reports.index' : 'admin.reports.index';

        return view('admin.reports', compact('complaints', 'filters', 'summary', 'printMode', 'layout', 'reportRoute'));
    }

    private function buildCharts(?User $reporter = null, bool $includeRoles = false): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $end = now()->endOfMonth();
        $complaints = Complaint::query()->when($reporter, fn (Builder $query) => $query->where('user_id', $reporter->id));

        $statusCounts = (clone $complaints)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statuses = [
            'labels' => ['Menunggu', 'Diverifikasi', 'Diproses', 'Selesai', 'Ditolak'],
            'values' => [
                (int) ($statusCounts['menunggu'] ?? 0),
                (int) ($statusCounts['diverifikasi'] ?? 0),
                (int) ($statusCounts['diproses'] ?? 0),
                (int) ($statusCounts['selesai'] ?? 0),
                (int) ($statusCounts['ditolak'] ?? 0),
            ],
        ];

        $monthlyLabels = [];
        $monthlyValues = [];

        for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
            $month = $start->copy()->addMonths($monthOffset);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyValues[] = (clone $complaints)
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        }

        $categoryFilter = function (Builder $query) use ($reporter, $start, $end): void {
            $query->whereBetween('created_at', [$start, $end]);

            if ($reporter) {
                $query->where('user_id', $reporter->id);
            }
        };

        $categoryCounts = Category::query()
            ->whereHas('complaints', $categoryFilter)
            ->withCount(['complaints' => function (Builder $query) use ($categoryFilter): void {
                $categoryFilter($query);
            }])
            ->orderByDesc('complaints_count')
            ->take(8)
            ->get();

        $charts = [
            'statuses' => $statuses,
            'monthly' => ['labels' => $monthlyLabels, 'values' => $monthlyValues],
            'categories' => [
                'labels' => $categoryCounts->pluck('nama')->all(),
                'values' => $categoryCounts->pluck('complaints_count')->map(fn ($count): int => (int) $count)->all(),
            ],
        ];

        if ($includeRoles) {
            $roleCounts = User::query()
                ->selectRaw('role, COUNT(*) as aggregate')
                ->groupBy('role')
                ->pluck('aggregate', 'role');
            $charts['roles'] = [
                'labels' => ['Super Admin', 'Guru', 'Teknisi', 'Siswa'],
                'values' => [
                    (int) ($roleCounts['super_admin'] ?? 0),
                    (int) ($roleCounts['guru'] ?? 0),
                    (int) ($roleCounts['teknisi'] ?? 0),
                    (int) (($roleCounts['user'] ?? 0) + ($roleCounts['siswa'] ?? 0)),
                ],
            ];
        }

        return $charts;
    }
}
