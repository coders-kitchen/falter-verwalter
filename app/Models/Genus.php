<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genus extends Model
{
    protected $table = 'genera';

    protected $fillable = [
        'subfamily_id',
        'tribe_id',
        'name',
    ];

    public function subfamily(): BelongsTo
    {
        return $this->belongsTo(Subfamily::class);
    }

    public function tribe(): BelongsTo
    {
        return $this->belongsTo(Tribe::class);
    }

    public function species(): HasMany
    {
        return $this->hasMany(Species::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }

    public function hierarchyPath(): string
    {
        $subfamily = $this->relationLoaded('subfamily') ? $this->subfamily : $this->subfamily()->with('family')->first();
        $family = $subfamily?->relationLoaded('family') ? $subfamily->family : $subfamily?->family;

        $parts = [
            $family?->name,
            $subfamily?->name,
        ];

        if ($this->tribe_id) {
            $tribe = $this->relationLoaded('tribe') ? $this->tribe : $this->tribe;
            $parts[] = $tribe?->name;
        }

        $parts[] = $this->name;

        return implode(' >> ', array_values(array_filter($parts)));
    }

    public function displayLabel(): string
    {
        $subfamily = $this->relationLoaded('subfamily') ? $this->subfamily : $this->subfamily()->with('family')->first();
        $family = $subfamily?->relationLoaded('family') ? $subfamily->family : $subfamily?->family;
        $tribe = $this->relationLoaded('tribe') ? $this->tribe : $this->tribe;

        $hierarchy = array_filter([
            $family?->name,
            $subfamily?->name,
            $tribe?->name,
        ]);

        if (empty($hierarchy)) {
            return $this->name;
        }

        return $this->name . ' (' . implode(' > ', $hierarchy) . ')';
    }
}
