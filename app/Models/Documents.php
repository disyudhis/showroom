<?php

namespace App\Models;

use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use MediaAlly;

    protected $fillable = [
        'url',
        'public_id',
        'car_id'
    ];


    public function car(){
        return $this->belongsTo(Cars::class, 'car_id');
    }
}
