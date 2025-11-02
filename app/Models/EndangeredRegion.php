<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EndangeredRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'description',
    ];

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_endangered_region')
            ->withTimestamps();
    }
}
