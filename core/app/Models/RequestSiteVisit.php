<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RequestSiteVisit extends Model
{
    protected $fillable = [
        'first_name',
        'email',
        'mobile_phone',
        'city_desc',
        'udf_16',
        'budget_from',
        'budget_to',
        'udf_17',
        'udf_18',
        'udf_6',
        'comments',
        'origin_from',
        'ip_address',
    ];

    protected $casts = [
        'udf_6' => 'date',
    ];

    public function scopeFilter(Builder $query, $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }
    }
}
