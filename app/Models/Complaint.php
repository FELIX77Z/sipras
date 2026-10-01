<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $table = 'pengaduans';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'sarana_id',
        'nomor',
        'judul',
        'lokasi',
        'deskripsi',
        'foto',
        'status',
        'prioritas',
        'response',
        'handled_by',
        'technician_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'sarana_id');
    }

    public function updates()
    {
        return $this->hasMany(ComplaintUpdate::class, 'pengaduan_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu' => 'Diajukan',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst((string) $this->status),
        };
    }

    public function priorityLabel(): string
    {
        return ucfirst((string) $this->prioritas ?? 'sedang');
    }
}
