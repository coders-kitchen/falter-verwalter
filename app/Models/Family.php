<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'subfamily',
        'genus',
        'tribe',
        'type',
        'description',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function species(): HasMany
    {
        return $this->hasMany(Species::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class, 'family_id');
    }

    /**
     * Get full taxonomic classification path
     */
    public function getFullClassificationAttribute(): string
    {
        $parts = [$this->name];

        if ($this->subfamily) {
            $parts[] = $this->subfamily;
        }

        if ($this->genus) {
            $parts[] = $this->genus;
        }

        if ($this->tribe) {
            $parts[] = $this->tribe;
        }

        return implode(' › ', $parts);
    }

    /**
     * Scope for butterfly families
     */
    public function scopeButterflies($query)
    {
        return $query->where('type', 'butterfly');
    }

    /**
     * Scope for plant families
     */
    public function scopePlants($query)
    {
        return $query->where('type', 'plant');
    }
}
