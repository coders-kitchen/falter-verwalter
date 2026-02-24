<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChangelogEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'title',
        'summary',
        'details',
        'audience',
        'published_at',
        'is_active',
        'commit_refs',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'commit_refs' => 'array',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPublic(Builder $query): Builder
    {
        return $query->whereIn('audience', ['public', 'both']);
    }

    public function scopeForAdmin(Builder $query): Builder
    {
        return $query->whereIn('audience', ['admin', 'both']);
    }
}
