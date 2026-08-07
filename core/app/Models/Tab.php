<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tab extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'subheading',
        'display_order',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('display_order');
    }
}
