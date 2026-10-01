<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifikasis';

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
        ];
    }

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'dibaca_pada',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
