<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportConfig extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'emails',
        'city_ids',
        'show_tso_attendance',
        'show_distributor_sales',
        'show_product_sales',
        'show_top_bottom_tso',
        'show_top_bottom_shop',
        'show_overall_sales',
        'is_active',
    ];
    
    protected $casts = [
        'city_ids' => 'array',
        'show_tso_attendance' => 'boolean',
        'show_distributor_sales' => 'boolean',
        'show_product_sales' => 'boolean',
        'show_top_bottom_tso' => 'boolean',
        'show_top_bottom_shop' => 'boolean',
        'show_overall_sales' => 'boolean',
        'is_active' => 'boolean',
    ];
}
