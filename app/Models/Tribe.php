<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tribe extends Model
{
    protected $fillable = [
        'subfamily_id',
        'name',
    ];

    public function subfamily(): BelongsTo
    {
        return $this->belongsTo(Subfamily::class);
    }

    public function genera(): HasMany
    {
        return $this->hasMany(Genus::class);
    }
}
