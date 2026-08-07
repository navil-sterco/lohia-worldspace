<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportInformation extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'value', 'file_path'];
    protected $table = 'support_information';

    public function scopeFilter(Builder $query, $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('key', 'like', "%{$filters['search']}%")
                  ->orWhere('value', 'like', "%{$filters['search']}%");
            });
        }
    }
}
