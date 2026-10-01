<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user) {
            Activity::create([
                'user_id' => $user->id,
                'aktivitas' => ($user->displayRole() ?: 'Pengguna').' login',
                'referensi' => 'dashboard',
            ]);
        }

        if ($user?->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        if ($user?->isGuru()) {
            return redirect()->intended(route('guru.dashboard', absolute: false));
        }

        if ($user?->isTechnician()) {
            return redirect()->intended(route('teknisi.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            Activity::create([
                'user_id' => $user->id,
                'aktivitas' => ($user->displayRole() ?: 'Pengguna').' logout',
                'referensi' => 'dashboard',
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }
}
