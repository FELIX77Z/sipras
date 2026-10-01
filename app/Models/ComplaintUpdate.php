<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintUpdate extends Model
{
    protected $table = 'tanggapans';

    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'status',
        'isi',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'pengaduan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
