<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_slug',
        'job_title',
        'name',
        'email',
        'phone',
        'best_time_to_call',
        'cv_path',
        'cv_original_name',
    ];
}
