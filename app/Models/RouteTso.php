<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteTso extends Model
{
        protected $table = 'route_tso';
    use HasFactory;


     protected $fillable = [
        'route_id',
        'tso_id',
        // Add any other fields you add in migration
    ];


     public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function tso()
    {
        return $this->belongsTo(Tso::class);
    }

}
