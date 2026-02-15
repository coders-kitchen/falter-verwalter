<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeciesPlant extends Model
{
    use HasFactory;

    protected $table = 'species_plant';

    protected $fillable = [
        'species_id',
        'plant_id',
        'is_nectar',
        'is_larval_host',
    ];

    protected $casts = [
        'is_nectar' => 'boolean',
        'is_larval_host' => 'boolean',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }
}
