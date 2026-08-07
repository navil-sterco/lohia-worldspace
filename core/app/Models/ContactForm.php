<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContactForm extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'location',
        'buyer_type',
        'budget',
        'property_type',
        'comment',
        'ip_address',
    ];

    public function scopeFilter(Builder $query, $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }
    }
}
