<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportConfig extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'city_ids',
        'show_tso_attendance',
        'show_distributor_sales',
        'show_overall_sales',
        'is_active',
        'cc_emails',
        'city_emails',
        'designation_ids',
    ];
    
    protected $casts = [
        'city_ids' => 'array',
        'city_emails' => 'array',
        'designation_ids' => 'array',
        'show_tso_attendance' => 'boolean',
        'show_distributor_sales' => 'boolean',
        'show_product_sales' => 'boolean',
        'show_top_bottom_tso' => 'boolean',
        'show_top_bottom_shop' => 'boolean',
        'show_overall_sales' => 'boolean',
        'is_active' => 'boolean',
    ];
}
