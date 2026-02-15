<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeciesRegion extends Pivot
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
    protected $table = 'species_region';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'species_id',
        'region_id',
        'conservation_status',
    ];

    protected $attributes = [
        'conservation_status' => 'nicht_gefährdet',
    ];

    /**
     * Conservation status constants and labels.
     */
    const CONSERVATION_STATUS = [
        'nicht_gefährdet' => 'Nicht gefährdet',
        'gefährdet' => 'Gefährdet',
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
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
