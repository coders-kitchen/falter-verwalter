<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionAreaLevel extends Model
{
    use HasFactory;

    public const MAP_ROLE_BACKGROUND = 'background';
    public const MAP_ROLE_DETAIL = 'detail';

    protected $fillable = [
        'name',
        'code',
        'sort_order',
        'map_role',
        'description',
    ];

    public function distributionAreas(): HasMany
    {
        return $this->hasMany(DistributionArea::class);
    }
}
