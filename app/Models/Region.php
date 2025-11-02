<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * Get all species in this region.
     *
     * @return BelongsToMany
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_region')
            ->withPivot('conservation_status')
            ->withTimestamps();
    }
}
