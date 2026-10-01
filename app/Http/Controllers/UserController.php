<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:aktif,nonaktif,inactive'],
        ]);
        $users = User::where('role', 'guru')
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')->orWhere('nip', 'like', '%'.$search.'%')))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User, 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nip' => ['nullable', 'string', 'max:50', 'unique:users,nip'],
            'major' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'major' => $request->major,
            'position' => $request->position,
            'phone' => $request->phone,
            'role' => 'guru',
            'status' => 'aktif',
            'password' => Hash::make($request->password),
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menambahkan Guru: '.$user->name,
            'referensi' => $user->id,
        ]);

        $this->notifyRoles(['super_admin'], 'Guru baru', $user->name.' telah ditambahkan sebagai Guru.');

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->isGuru(), 404);

        return view('admin.users.form', ['user' => $user, 'mode' => 'edit']);
    }

    public function show(User $user): View
    {
        abort_unless($user->isGuru(), 404);

        return view('users.show', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isGuru(), 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nip')->ignore($user->id)],
            'major' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'major' => $request->major,
            'position' => $request->position,
            'phone' => $request->phone,
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Mengedit Guru: '.$user->name,
            'referensi' => $user->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->complaints()->exists()) {
            return back()->with('error', 'Akun tidak dapat dihapus karena memiliki riwayat pengaduan.');
        }

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menghapus pengguna',
            'referensi' => $user->id,
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil dihapus.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless($user->isGuru(), 404);
        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => $user->status === 'aktif' ? 'Mengaktifkan Guru' : 'Menonaktifkan Guru',
            'referensi' => $user->id,
        ]);

        return back()->with('success', 'Status pengguna berhasil diperbarui.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        abort_unless($user->isGuru(), 404);

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Tautan reset password telah dikirim ke email pengguna.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function impersonate(User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);
        abort_unless($user->isGuru() || $user->isUser(), 404);
        abort_unless($user->isActive(), 403);

        $superAdminId = Auth::id();
        session(['super_admin_original_id' => $superAdminId, 'impersonated_user_id' => $user->id]);
        Auth::login($user);

        Activity::create([
            'user_id' => $superAdminId,
            'aktivitas' => 'Super Admin masuk sebagai '.($user->isGuru() ? 'Guru: ' : 'Siswa: ').$user->name,
            'referensi' => $user->id,
        ]);

        return redirect()->route($user->isGuru() ? 'guru.dashboard' : 'user.dashboard')->with('success', 'Anda sedang masuk sebagai '.($user->isGuru() ? 'Guru' : 'Siswa').'.');
    }

    public function leaveImpersonation(): RedirectResponse
    {
        $originalId = session('super_admin_original_id');

        if ($originalId) {
            Auth::loginUsingId($originalId);
            session()->forget(['super_admin_original_id', 'impersonated_user_id']);

            return redirect()->route('admin.dashboard')->with('success', 'Kembali ke dashboard Super Admin.');
        }

        return redirect()->route('admin.dashboard');
    }

    public function students(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:aktif,nonaktif,inactive'],
        ]);
        $students = User::whereIn('role', ['user', 'siswa'])
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')->orWhere('nis', 'like', '%'.$search.'%')->orWhere('nisn', 'like', '%'.$search.'%')))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        $view = Auth::user()?->isGuru() ? 'guru.students.index' : 'admin.students.index';

        return view($view, compact('students', 'filters'));
    }

    public function showStudent(User $user): View
    {
        abort_unless($user->isUser(), 404);

        return view('users.show', ['user' => $user]);
    }

    public function createStudent(): View
    {
        $layout = Auth::user()?->isSuperAdmin() ? 'layouts.admin' : 'layouts.guru';

        return view('guru.students.form', ['user' => new User, 'mode' => 'create', 'layout' => $layout]);
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nis' => ['nullable', 'required_without:nisn', 'string', 'max:50', Rule::unique('users', 'nis'), Rule::unique('users', 'nisn')],
            'nisn' => ['nullable', 'required_without:nis', 'string', 'max:50', Rule::unique('users', 'nisn'), Rule::unique('users', 'nis')],
            'class' => ['required', 'string', 'max:50'],
            'major' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'min:8'],
        ]);

        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'class' => $request->class,
            'major' => $request->major,
            'phone' => $request->phone,
            'role' => 'user',
            'status' => 'aktif',
            'password' => Hash::make($request->password),
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menambahkan Siswa: '.$student->name,
            'referensi' => $student->id,
        ]);

        $this->notifyRoles(['super_admin', 'guru'], 'Siswa baru', $student->name.' telah ditambahkan sebagai Siswa.');

        return redirect()->route($this->studentRoute('index'))->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function editStudent(User $user): View
    {
        abort_unless($user->isUser(), 404);

        $layout = Auth::user()?->isSuperAdmin() ? 'layouts.admin' : 'layouts.guru';

        return view('guru.students.form', ['user' => $user, 'mode' => 'edit', 'layout' => $layout]);
    }

    public function updateStudent(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'nis' => ['nullable', 'required_without:nisn', 'string', 'max:50', Rule::unique('users', 'nis')->ignore($user->id), Rule::unique('users', 'nisn')->ignore($user->id)],
            'nisn' => ['nullable', 'required_without:nis', 'string', 'max:50', Rule::unique('users', 'nisn')->ignore($user->id), Rule::unique('users', 'nis')->ignore($user->id)],
            'class' => ['required', 'string', 'max:50'],
            'major' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user->update($request->only(['name', 'email', 'nis', 'nisn', 'class', 'major', 'phone']));

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Mengedit Siswa: '.$user->name,
            'referensi' => $user->id,
        ]);

        return redirect()->route($this->studentRoute('index'))->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroyStudent(User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        if ($user->complaints()->exists()) {
            return back()->with('error', 'Siswa tidak dapat dihapus karena memiliki riwayat pengaduan.');
        }

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menghapus data siswa',
            'referensi' => $user->id,
        ]);

        $user->delete();

        return redirect()->route($this->studentRoute('index'))->with('success', 'Siswa berhasil dihapus.');
    }

    public function toggleStudentStatus(User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        $user->status = $user->isActive() ? 'nonaktif' : 'aktif';
        $user->save();

        Activity::create([
            'user_id' => Auth::id(),
            'aktivitas' => $user->isActive() ? 'Mengaktifkan Siswa' : 'Menonaktifkan Siswa',
            'referensi' => $user->id,
        ]);

        return back()->with('success', 'Status siswa berhasil diperbarui.');
    }

    public function resetStudentPassword(User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Tautan reset password telah dikirim ke email siswa.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    private function studentRoute(string $action): string
    {
        return Auth::user()?->isSuperAdmin() ? 'admin.students.'.$action : 'guru.students.'.$action;
    }

    private function notifyRoles(array $roles, string $title, string $message): void
    {
        User::whereIn('role', $roles)
            ->where('status', 'aktif')
            ->each(function (User $recipient) use ($title, $message): void {
                Notification::create([
                    'user_id' => $recipient->id,
                    'judul' => $title,
                    'pesan' => $message,
                ]);
            });
    }
}
