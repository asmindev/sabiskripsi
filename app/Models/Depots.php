<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depots extends Model
{
    //
    protected $fillable = [
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'kapasitas',
        'status',
    ];
}
