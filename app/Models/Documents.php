<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    protected $fillable = [
        'image',
        'car_id'
    ];


    public function car(){
        return $this->belongsTo(Cars::class);
    }
}
