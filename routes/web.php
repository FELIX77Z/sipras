<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureActiveUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $hasReportsTable = Schema::hasTable('pengaduans');
    $total = $hasReportsTable ? DB::table('pengaduans')->count() : 0;

    return view('landing', ['stats' => [
        ['value' => $total, 'label' => 'Total Pengaduan'],
        ['value' => $hasReportsTable ? DB::table('pengaduans')->where('status', 'menunggu')->count() : 0, 'label' => 'Menunggu Tindakan'],
        ['value' => $hasReportsTable ? DB::table('pengaduans')->where('status', 'diproses')->count() : 0, 'label' => 'Sedang Diproses'],
        ['value' => $hasReportsTable ? DB::table('pengaduans')->where('status', 'selesai')->count() : 0, 'label' => 'Telah Selesai'],
    ]]);
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
});

Route::middleware(['auth', EnsureActiveUser::class])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isGuru()) {
            return redirect()->route('guru.dashboard');
        }

        if ($user->isTechnician()) {
            return redirect()->route('teknisi.dashboard');
        }

        if ($user->isUser() || $user->role === null) {
            return redirect()->route('user.dashboard');
        }

        return redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::middleware(['role:user'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::post('/impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate');
    Route::post('/leave-impersonation', [UserController::class, 'leaveImpersonation'])->name('leave-impersonation');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/response', [ComplaintController::class, 'storeResponse'])->name('complaints.response');
    Route::put('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
});

Route::middleware(['auth', EnsureActiveUser::class, 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/students', [UserController::class, 'students'])->name('students.index');
    Route::get('/students/create', [UserController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [UserController::class, 'storeStudent'])->name('students.store');
    Route::get('/students/{user}', [UserController::class, 'showStudent'])->name('students.show');
    Route::get('/students/{user}/edit', [UserController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{user}', [UserController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{user}', [UserController::class, 'destroyStudent'])->name('students.destroy');
    Route::post('/students/{user}/toggle-status', [UserController::class, 'toggleStudentStatus'])->name('students.toggle-status');
    Route::post('/students/{user}/reset-password', [UserController::class, 'resetStudentPassword'])->name('students.reset-password');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/activities', [DashboardController::class, 'activities'])->name('activities.index');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
    Route::get('/technicians', [TechnicianController::class, 'index'])->name('technicians.index');
    Route::post('/technicians', [TechnicianController::class, 'store'])->name('technicians.store');
    Route::patch('/complaints/{complaint}/technician', [TechnicianController::class, 'assign'])->name('complaints.technician');
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
    Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
    Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
    Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');
    Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
    Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
});

Route::middleware(['auth', EnsureActiveUser::class, 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'guru'])->name('dashboard');
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');
    Route::get('/students', [UserController::class, 'students'])->name('students.index');
    Route::get('/students/create', [UserController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [UserController::class, 'storeStudent'])->name('students.store');
    Route::get('/students/{user}', [UserController::class, 'showStudent'])->name('students.show');
    Route::get('/students/{user}/edit', [UserController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{user}', [UserController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{user}', [UserController::class, 'destroyStudent'])->name('students.destroy');
    Route::post('/students/{user}/toggle-status', [UserController::class, 'toggleStudentStatus'])->name('students.toggle-status');
    Route::post('/students/{user}/reset-password', [UserController::class, 'resetStudentPassword'])->name('students.reset-password');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
    Route::get('/technicians', [TechnicianController::class, 'index'])->name('technicians.index');
    Route::post('/technicians', [TechnicianController::class, 'store'])->name('technicians.store');
    Route::patch('/complaints/{complaint}/technician', [TechnicianController::class, 'assign'])->name('complaints.technician');
});

Route::middleware(['auth', EnsureActiveUser::class, 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [TechnicianController::class, 'dashboard'])->name('dashboard');
    Route::put('/complaints/{complaint}/status', [TechnicianController::class, 'updateStatus'])->name('complaints.status');
});

Route::middleware(['auth', EnsureActiveUser::class, 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
});

require __DIR__.'/auth.php';
