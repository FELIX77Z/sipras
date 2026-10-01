<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'aktivitass';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'aktivitas',
        'referensi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
