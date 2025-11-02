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

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
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
