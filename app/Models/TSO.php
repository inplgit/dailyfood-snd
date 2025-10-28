<?php

namespace App\Models;

use App\Scopes\Common\CommonScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;
use DB;

class TSO extends Model
{
    use HasFactory;

    protected $table = "tso";
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();
        // Global Scope
     //   static::addGlobalScope(new CommonScope);
    }

    /** Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query)
    {
        return $query->where('status',1);
    }
    public function scopeActive($query)
    {
        return $query->where('active',1);
    }
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
    public function attendence()
    {
        return $this->hasMany(Attendence::class , 'user_id' , 'user_id');
    }
    public function shop_visits()
    {
        return $this->belongsTo(Attendence::class, 'user_id', 'user_id');
    }

    public function cities()
    {
        return $this->belongsTo(City::class,'city','id');
    }

public function locations()
{
    return $this->hasMany(TsoLocation::class, 'tso_id'); 
}


    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }




    public function Route()
    {
        return $this->hasMany(Route::class,'tso_id');//->select('id','day');
    }
    public function routeDayWise()
    {
        return $this->hasMany(Route::class,'tso_id')->whereHas('routeday', function ($query) {
            $query->where('day',date('l'));
        });//->select('id','day');
    }

    public function shop()
    {
        return $this->hasMany(Shop::class,'tso_id');//->select('id','day');
    }

    public function UserDistributor()
    {
        return $this->hasMany(UsersDistributors::class,'user_id','user_id');
    }

    public function distributors()
    {
        return $this->belongsToMany(Distributor::class, 'users_distributors', 'user_id', 'distributor_id');
    }

public function saleOrders()
{
    return $this->hasMany(SaleOrder::class, 'tso_id');
}

    public function activityLog()
    {
        return $this->morphMany(ActivityLog::class ,'table');
    }


    /** Scopes */
    // get unique no
    function scopeUniqueNo($query)
    {
   //  $id = $query->max('id')+1;
 //    $number = sprintf('%03d',$id);
     $number =   DB::table($this->table)->max('id')+1;
     return  $number = sprintf('%03d',$number);

    }

}
