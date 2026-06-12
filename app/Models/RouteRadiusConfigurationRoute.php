<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteRadiusConfigurationRoute extends Model
{
    use HasFactory;

    protected $table = 'route_radius_configuration_routes';

    protected $fillable = [
        'configuration_id',
        'route_id',
    ];

    public function configuration()
    {
        return $this->belongsTo(RouteRadiusConfiguration::class, 'configuration_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
