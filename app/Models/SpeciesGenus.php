<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeciesGenus extends Model
{
    use HasFactory;

    protected $table = 'species_genus';

    protected $fillable = [
        'species_id',
        'genus_id',
        'is_nectar',
        'is_larval_host',
        'adult_preference',
        'larval_preference',
    ];

    protected $casts = [
        'is_nectar' => 'boolean',
        'is_larval_host' => 'boolean',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function genus(): BelongsTo
    {
        return $this->belongsTo(Genus::class);
    }
}
