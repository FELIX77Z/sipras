<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'status',
    'nis',
    'nisn',
    'nip',
    'class',
    'major',
    'phone',
    'position',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
        ];
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'technician_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'teknisi';
    }

    public function isUser(): bool
    {
        return in_array($this->role, ['user', 'siswa'], true);
    }

    public function isActive(): bool
    {
        return ! in_array($this->status, ['nonaktif', 'inactive'], true);
    }

    public function displayRole(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'guru' => 'Guru',
            'teknisi' => 'Teknisi',
            'siswa', 'user' => 'Siswa',
            default => ucfirst((string) $this->role),
        };
    }
}
