<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TsoLocation extends Model
{
    use HasFactory;
      protected $fillable = [
        'tso_id',
        'location_name',
        'latitude',
        'longitude',
        'radius'
    ];

    public function tso()
    {
        return $this->belongsTo(TSO::class);
    }
}
