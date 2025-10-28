<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class Route extends Model
{
    use HasFactory;
    public $table ='routes';
    protected $fillable = ['route_name','distributor_id','tso_id','day','status','username'];
    protected $primarykey = 'id';


    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->username = Auth::user()->name;
        });
    }


     // get active record
     function scopeStatus($query)
     {
        return $query->where('status',1);
     }

     public function Distributor()
     {
        return $this->belongsTo(Distributor::class);
     }

     public function TSO()
     {
        return $this->belongsTo(TSO::class,'tso_id');
     }

     public function shops()
     {
      return $this->hasMany(Shop::class, 'route_id');
     }
     public function subroute()
     {
      return $this->hasOne(SubRoutes::class);
     }


     public function RouteDay()
     {
         return $this->hasMany(RouteDay::class,'route_id');
     }
     public function activityLog()
     {
         return $this->morphMany(ActivityLog::class ,'table');
     }

 public function routeTso()
    {
        return $this->hasMany(RouteTso::class, 'route_id');
    }

    public function routeDays()
{
    return $this->hasMany(RouteDay::class, 'route_id');
}

public function routeTsos()
{
    return $this->hasMany(RouteTso::class, 'route_id');
}

public function tsos()
{
    return $this->belongsToMany(TSO::class, 'route_tso', 'route_id', 'tso_id');
}


    //  public function getdayAttribute()
    // {
    //     return $this->RouteDay->pluck('day')->implode(', ');
    // }
}
