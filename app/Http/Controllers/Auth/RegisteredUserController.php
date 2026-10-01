<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['nullable', 'required_without:nisn', 'string', 'max:50', Rule::unique('users', 'nis'), Rule::unique('users', 'nisn')],
            'nisn' => ['nullable', 'required_without:nis', 'string', 'max:50', Rule::unique('users', 'nisn'), Rule::unique('users', 'nis')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'class' => ['required', 'string', 'max:50'],
            'major' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agreement' => ['required', 'accepted'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nis' => $validated['nis'] ?? null,
            'nisn' => $validated['nisn'] ?? null,
            'email' => $validated['email'],
            'class' => $validated['class'],
            'major' => $validated['major'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => 'user',
            'status' => 'aktif',
        ]);

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Siswa mendaftar: '.$user->name,
            'referensi' => 'user',
        ]);

        User::whereIn('role', ['super_admin', 'guru'])
            ->where('status', 'aktif')
            ->each(function (User $recipient) use ($user): void {
                Notification::create([
                    'user_id' => $recipient->id,
                    'judul' => 'Siswa baru',
                    'pesan' => $user->name.' mendaftar melalui formulir publik.',
                ]);
            });

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Registrasi berhasil. Silakan login menggunakan akun Anda.');
    }
}
