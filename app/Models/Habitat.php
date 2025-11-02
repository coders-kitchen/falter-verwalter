<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habitat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'description',
        'level',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Habitat::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Habitat::class, 'parent_id');
    }

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_habitat')
            ->withTimestamps();
    }

    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'plant_habitat')
            ->withTimestamps();
    }
}
