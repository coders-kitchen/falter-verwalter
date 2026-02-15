<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DistributionArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_distribution_areas')
            ->using(SpeciesDistributionArea::class)
            ->withPivot('status', 'threat_category_id', 'user_id')
            ->withTimestamps();
    }
}
