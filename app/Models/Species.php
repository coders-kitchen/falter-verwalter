<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Species extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'family_id',
        'name',
        'scientific_name',
        'size_category',
        'color_description',
        'special_features',
        'gender_differences',
        'generations_per_year',
        'hibernation_stage',
        'pupal_duration_days',
        'red_list_status_de',
        'red_list_status_eu',
        'abundance_trend',
        'protection_status',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function generations(): HasMany
    {
        return $this->hasMany(Generation::class)->orderBy('generation_number');
    }

    public function distributionAreas(): BelongsToMany
    {
        return $this->belongsToMany(DistributionArea::class, 'species_distribution')
            ->withTimestamps();
    }

    public function habitats(): BelongsToMany
    {
        return $this->belongsToMany(Habitat::class, 'species_habitat')
            ->withTimestamps();
    }

    public function hostPlants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'species_plant', 'species_id', 'plant_id')
            ->withPivot('plant_type')
            ->withTimestamps();
    }

    public function endangeredRegions(): BelongsToMany
    {
        return $this->belongsToMany(EndangeredRegion::class, 'species_endangered_region')
            ->withTimestamps();
    }

    /**
     * Get all regions where this species occurs.
     * This relationship replaces the old endangeredRegions() which only showed
     * species marked as endangered. Now regions are separate from conservation status.
     *
     * @return BelongsToMany
     */
    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(
            Region::class,
            'species_region',
            'species_id',
            'region_id'
        )
            ->using(SpeciesRegion::class)
            ->withPivot('conservation_status')
            ->withTimestamps();
    }

    /**
     * Get only the regions where this species is marked as endangered.
     * Filters the regions() relationship by conservation_status = 'gefährdet'.
     *
     * @return BelongsToMany
     */
    public function endangeredRegionsList(): BelongsToMany
    {
        return $this->regions()
            ->wherePivot('conservation_status', 'gefährdet');
    }
}
