<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TsoTrackingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tso_id',
        'distributor_id',
        'latitude',
        'longitude',
        'sync_date_time',
        'local_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tso()
    {
        return $this->belongsTo(TSO::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
