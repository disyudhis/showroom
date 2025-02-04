<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;

    const STATUS_SOLD = "SOLD";
    const STATUS_AVAILABLE = "AVAILABLE";

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
        'customers_id',
        'odo_service',
        'status'
    ];

    public function customer(){
        return $this->belongsTo(Customers::class, 'customers_id');
    }

    public function images(){
        return $this->hasMany(Image::class, 'car_id');
    }

    public function documents(){
        return $this->hasMany(Documents::class, 'car_id');
    }

    public function hpps(){
        return $this->hasMany(Hpp::class, 'car_id');
    }

    public function getStatusColorAttribute() {
        if($this->status == self::STATUS_SOLD){
            return 'bg-gray-500 text-white';
        }else {
            return 'bg-green-500 text-white';
        }
    }
}
