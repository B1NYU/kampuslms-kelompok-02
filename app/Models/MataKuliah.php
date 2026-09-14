<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'sks',
        'dosen',
        'semester',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'sks' => 'integer',
    ];
}
