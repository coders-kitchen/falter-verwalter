<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'life_form_id',
        'name',
        'scientific_name',
        'family_genus',
        'light_number',
        'temperature_number',
        'continentality_number',
        'reaction_number',
        'moisture_number',
        'moisture_variation',
        'nitrogen_number',
        'bloom_months',
        'bloom_color',
        'plant_height_cm',
        'lifespan',
        'location',
        'is_native',
        'is_invasive',
        'threat_status',
        'persistence_organs',
    ];

    protected $casts = [
        'bloom_months' => 'json',
        'is_native' => 'boolean',
        'is_invasive' => 'boolean',
    ];

    public function lifeForm(): BelongsTo
    {
        return $this->belongsTo(LifeForm::class);
    }

    public function habitats(): BelongsToMany
    {
        return $this->belongsToMany(Habitat::class, 'plant_habitat')
            ->withTimestamps();
    }

    public function speciesAsHostPlant(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_plant', 'plant_id', 'species_id')
            ->withPivot('plant_type')
            ->withTimestamps();
    }
}
