<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteDay extends Model
{
    use HasFactory;

    protected $fillable = ['route_id','day'];

    public function Route()
    {
        return $this->belongsTo(Route::class,'route_id');
    }

    public function activeRoutedays()
    {
        return $this->hasMany(RouteDay::class, 'route_id')
            ->where('day', date('l'));
    }
}
