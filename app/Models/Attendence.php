<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','in','out', 'latitude_in', 'longitude_in', 'latitude_out', 'longitude_out','distributor_id','tso_id','route_id','date'];

    public function usersLocation()
    {
        // return $this->morphMany(UsersLocation::class ,'table');
        return $this->morphOne(UsersLocation::class ,'table');
    }

    public function tso()
    {
        return $this->belongsTo(TSO::class,'tso_id','id');
    }
    // public function distributor()
    // {
    //     return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    // }

    public function tsoRelation()
    {
        return $this->belongsTo(TSO::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }


}
