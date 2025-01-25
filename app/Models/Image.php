<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'image',
        'cars_id'
    ];

    public function car(){
        return $this->belongsTo(Cars::class, 'cars_id');
    }
}
