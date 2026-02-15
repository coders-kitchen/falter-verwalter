<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeciesDistributionArea extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'species_distribution_areas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'species_id',
        'distribution_area_id',
        'status',
        'threat_category_id',
        'user_id'
    ];

    /**
     * status constants and labels.
     */
    const STATUS = [
        'heimisch' => 'heimisch',
        'ausgestorben' => 'ausgestorben',
        'neobiotisch' => 'neobiotisch'
    ];

    /**
     * Get the species associated with this region mapping.
     *
     * @return BelongsTo
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the region associated with this species mapping.
     *
     * @return BelongsTo
     */
    public function distributionArea(): BelongsTo
    {
        return $this->belongsTo(DistributionArea::class);
    }

    /**
     * Get the region associated with this species mapping.
     *
     * @return BelongsTo
     */
    public function threatCategory(): BelongsTo
    {
        return $this->belongsTo(ThreatCategory::class);
    }
}
