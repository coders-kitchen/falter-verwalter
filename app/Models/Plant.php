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
        'family_id',
        'name',
        'scientific_name',
        'family_genus',
        'light_number',
        'salt_number',
        'temperature_number',
        'continentality_number',
        'reaction_number',
        'moisture_number',
        'moisture_variation',
        'nitrogen_number',
        'bloom_start_month',
        'bloom_end_month',
        'bloom_color',
        'plant_height_cm_from',
        'plant_height_cm_until',
        'lifespan',
        'location',
        'is_native',
        'is_invasive',
        'threat_status',
        'persistence_organs',
    ];

    protected $casts = [
        'is_native' => 'boolean',
        'is_invasive' => 'boolean',
    ];

    public function lifeForm(): BelongsTo
    {
        return $this->belongsTo(LifeForm::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
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

    /**
     * Get the month name for display
     */
    public static function getMonthName($monthNumber): string
    {
        $months = [
            1 => 'Januar',
            2 => 'Februar',
            3 => 'März',
            4 => 'April',
            5 => 'Mai',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'August',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Dezember',
        ];

        return $months[$monthNumber] ?? '';
    }
}
