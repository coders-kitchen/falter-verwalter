<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ThreatCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'description',
        'rank',
        'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'threat_categories';


    public function speciesEndageredStatus(): BelongsToMany
    {
        return $this->belongsToMany(SpeciesEndageredStatus::class, 'species_endagered_status')
            ->withTimestamps();
    }
}
