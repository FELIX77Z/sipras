<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'kategoris';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'kategori_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'kategori_id');
    }
}
