<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreatCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'description',
        'rank',
        'color_code',
        'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'threat_categories';


    public function speciesDistributionAreas(): HasMany
    {
        return $this->hasMany(SpeciesDistributionArea::class);
    }
}
