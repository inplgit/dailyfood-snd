<?php

namespace App\Models;

use App\Scopes\Common\CommonScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['saleOrderDataList'];


    public function getSaleOrderDataListAttribute()
    {
        return $this->hasMany(SaleOrderData::class, 'so_id', 'id')->where('status', 1)->latest()->get();
    }

    public function getDcDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected static function boot()
    {
        parent::boot();
        // Global Scope 
        static::addGlobalScope(new CommonScope);
    }

    // Relationships
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
    public function tso()
    {
        return $this->belongsTo(TSO::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function saleOrderData()
    {
        return $this->hasMany(SaleOrderData::class, 'so_id', 'id');
    }

    // public function UserDistributor()
    // {
    //     return $this->hasMany(UserDistributor::class, 'so_id', 'id');
    // }


    public function SalesReturn()
    {
        return $this->hasMany(SalesReturn::class, 'so_id', 'id');
    }

    /** Scopes */
    // get unique no
    function scopeUniqueNo($query)
    {

        $id = $query->withoutGlobalScopes()->max('id') + 1;
        return  $number = 'so' . sprintf('%03d', $id);
    }
    function scopeStatus($query)
    {
        return $query->where('status', 1);
    }
    function scopeExecute($query, $val)
    {
        return $query->where('excecution', $val);
    }
    public function usersLocation()
    {
        return $this->morphMany(UsersLocation::class, 'table');
    }
}
