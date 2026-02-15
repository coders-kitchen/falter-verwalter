<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subfamily extends Model
{
    protected $fillable = [
        'family_id',
        'name',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function tribes(): HasMany
    {
        return $this->hasMany(Tribe::class);
    }

    public function genera(): HasMany
    {
        return $this->hasMany(Genus::class);
    }
}
