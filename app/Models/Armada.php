<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Armada extends Model
{
    protected $fillable = ['namaTruk', 'nomorPlat', 'kapasitas', 'status', 'driver', 'lastMaintenance'];

    /**
     * Get the TPS assigned to this armada.
     */
    public function tps(): HasMany
    {
        return $this->hasMany(TPS::class, 'armada_id');
    }
}
