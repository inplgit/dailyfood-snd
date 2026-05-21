<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteRadiusConfiguration extends Model
{
    use HasFactory;

    protected $table = 'route_radius_configurations';

    protected $fillable = [
        'distributor_id',
        'tso_id',
        'radius',
        'created_by',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function tso()
    {
        return $this->belongsTo(TSO::class, 'tso_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function routes()
    {
        return $this->belongsToMany(
            Route::class,
            'route_radius_configuration_routes',
            'configuration_id',
            'route_id'
        );
    }
}
