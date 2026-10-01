<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'saranas';

    protected $fillable = [
        'kategori_id',
        'nama',
        'kode',
        'lokasi',
        'kondisi',
        'jumlah',
        'foto',
        'deskripsi',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'sarana_id');
    }
}
