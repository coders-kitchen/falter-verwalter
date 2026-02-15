<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'species_id',
        'generation_number',
        'larva_start_month',
        'larva_end_month',
        'flight_start_month',
        'flight_end_month',
        'host_plants',
        'nectar_plants',
        'larval_host_plants',
        'description',
    ];

    protected $casts = [
        'generation_number' => 'integer',
        'larva_start_month' => 'integer',
        'larva_end_month' => 'integer',
        'flight_start_month' => 'integer',
        'flight_end_month' => 'integer',
        'host_plants' => 'array',
        'nectar_plants' => 'array',
        'larval_host_plants' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Generation $generation) {
            if (empty($generation->generation_number)) {
                $generation->generation_number = static::nextGenerationNumber((int) $generation->species_id);
            }
        });

        static::created(function (Generation $generation) {
            static::syncSpeciesGenerationsPerYear((int) $generation->species_id);
        });

        static::updated(function (Generation $generation) {
            if ($generation->wasChanged('species_id')) {
                $oldSpeciesId = (int) $generation->getOriginal('species_id');
                $newSpeciesId = (int) $generation->species_id;

                static::renumberForSpecies($oldSpeciesId);
                static::renumberForSpecies($newSpeciesId);
                static::syncSpeciesGenerationsPerYear($oldSpeciesId);
                static::syncSpeciesGenerationsPerYear($newSpeciesId);
            }
        });

        static::deleted(function (Generation $generation) {
            static::renumberForSpecies((int) $generation->species_id);
            static::syncSpeciesGenerationsPerYear((int) $generation->species_id);
        });
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public static function nextGenerationNumber(int $speciesId): int
    {
        return ((int) static::where('species_id', $speciesId)->max('generation_number')) + 1;
    }

    public static function renumberForSpecies(int $speciesId): void
    {
        if ($speciesId <= 0) {
            return;
        }

        $generations = static::where('species_id', $speciesId)
            ->orderBy('generation_number')
            ->orderBy('id')
            ->get(['id', 'generation_number']);

        $nextNumber = 1;
        foreach ($generations as $generation) {
            if ((int) $generation->generation_number !== $nextNumber) {
                static::whereKey($generation->id)->update(['generation_number' => $nextNumber]);
            }
            $nextNumber++;
        }
    }

    public static function syncSpeciesGenerationsPerYear(int $speciesId): void
    {
        if ($speciesId <= 0) {
            return;
        }

        $count = static::where('species_id', $speciesId)->count();
        Species::whereKey($speciesId)->update(['generations_per_year' => $count]);
    }

    /**
     * Get all plants used by this generation (both nectar and larval host plants)
     */
    public function plants()
    {
        $plantIds = array_unique(array_merge(
            $this->nectar_plants ?? [],
            $this->larval_host_plants ?? []
        ));

        if (empty($plantIds)) {
            return collect([]);
        }

        return Plant::whereIn('id', $plantIds)->get();
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
