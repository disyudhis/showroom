<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hpp extends Model
{
    protected $fillable = [
        'car_id',
        'deskripsi',
        'total'
    ];

    protected $casts = [
        'deskripsi' => 'array',
        'total' => 'string'
    ];
}
