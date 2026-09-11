<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChannelPartner extends Model
{
    protected $fillable = [
        'name',
        'pan',
        'country_code',
        'phone',
        'email',
        'address',
        'work_profile',
        'other_organizations',
        'region_of_operations',
        'sales_team_member_name',
        'company_name',
        'date_of_establishment',
        'organization_type',
        'association_member',
        'business_type',
        'registration_certificate_path',
        'rera_certificate_path',
        'registered_address',
        'company_pan_details',
        'gst_certificate_path',
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
