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
        'genus_id',
        'threat_category_id',
        'name',
        'scientific_name',
        'family_genus',
        'light_number',
        'light_number_state',
        'salt_number',
        'salt_number_state',
        'temperature_number',
        'temperature_number_state',
        'continentality_number',
        'continentality_number_state',
        'reaction_number',
        'reaction_number_state',
        'moisture_number',
        'moisture_number_state',
        'moisture_variation',
        'moisture_variation_state',
        'nitrogen_number',
        'nitrogen_number_state',
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
        'heavy_metal_resistance',
        'persistence_organs',
    ];

    protected $casts = [
        'is_native' => 'boolean',
        'is_invasive' => 'boolean',
    ];

    public const HEAVY_METAL_RESISTANCE_LEVELS = [
        'nicht schwermetallresistent',
        'mäßig schwermetallresistent',
        'ausgesprochen schwermetallresistent',
    ];

    public function indicatorDisplay(string $field): string
    {
        $state = $this->{"{$field}_state"} ?? 'numeric';

        if ($state === 'x') {
            return 'X';
        }

        if ($state === 'unknown') {
            return '?';
        }

        $value = $this->{$field};
        return $value === null ? '—' : (string) $value;
    }

    public function lifeForm(): BelongsTo
    {
        return $this->belongsTo(LifeForm::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function genus(): BelongsTo
    {
        return $this->belongsTo(Genus::class);
    }

    public function threatCategory(): BelongsTo
    {
        return $this->belongsTo(ThreatCategory::class);
    }

    public function habitats(): BelongsToMany
    {
        return $this->belongsToMany(Habitat::class, 'plant_habitat')
            ->withTimestamps();
    }

    public function speciesAsHostPlant(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_plant', 'plant_id', 'species_id')
            ->withPivot('is_nectar', 'is_larval_host')
            ->withTimestamps();
    }

    public function speciesAsNectarPlant(): BelongsToMany
    {
        return $this->speciesAsHostPlant()->wherePivot('is_nectar', true);
    }

    public function speciesAsLarvalHostPlant(): BelongsToMany
    {
        return $this->speciesAsHostPlant()->wherePivot('is_larval_host', true);
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
