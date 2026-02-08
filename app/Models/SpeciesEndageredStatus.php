<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeciesEndageredStatus extends Pivot
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
    protected $table = 'species_endagered_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'species_id',
        'distribution_area_id',
        'threat_status_definition_id',
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
        return $this->belongsTo(distributionArea::class);
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
