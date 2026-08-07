<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SeoSetting extends Model
{
    protected $fillable = [
        'meta_title',
        'meta_description',
        'keywords',
        'search_terms',
        'canonical_url',
        'url',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'og_url',
    ];

    protected $casts = [
        'keywords' => 'array',
        'search_terms' => 'array',
    ];

    public function scopeFilter(Builder $query, $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('meta_title', 'like', "%{$filters['search']}%");
            })->orWhere('url', 'like', "%{$filters['search']}%");
        }
    }
}
