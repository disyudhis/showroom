<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mobil',
        'deskripsi',
        'no_mesin',
        'pajak_tahunan',
        'pajak_5tahun',
        'no_polisi',
        'tahun_pembuatan',
        'last_service_date',
        'odo',
        'brand',
        'customers_id'
    ];

    public function customer(){
        return $this->belongsTo(Customers::class, 'customers_id');
    }

    public function images(){
        return $this->hasMany(Image::class);
    }

    public function documents(){
        return $this->hasMany(Documents::class, 'car_id');
    }
}
