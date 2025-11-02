<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LifeForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'examples',
    ];

    protected $casts = [
        'examples' => 'json',
    ];

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }
}
