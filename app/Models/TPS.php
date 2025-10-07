<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TPS extends Model
{
    protected $table = 't_p_s';

    protected $fillable = [
        'nama',
        'alamat',
        'area',
        'status',
        'kapasitas',
        'latitude',
        'longitude',
        'armada_id'
    ];

    /**
     * Get the armada that owns the TPS.
     */
    public function armada(): BelongsTo
    {
        return $this->belongsTo(Armada::class, 'armada_id');
    }
}
