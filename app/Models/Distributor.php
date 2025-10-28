<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_code',
        'custom_code',
        'distributor_name',
        'email',
        'phone',
        'contact_person',
        'alt_phone',
        'address',
        'city_id',
        'city',
        'zip',
        'zone_id',
        'note',
        'pricing_type_id',
        'min_discount',
        'max_discount',
        'map',
        'location_title',
        'location_latitude',
        'location_longitude',
        'location_radius',
        'username',
        'status',
         'level1',
         'parent_code',
         'level2',
         'level3',
         'level3',
         'distributor_sub_code'
    ];
    protected $primarykey = 'id';


    /**
     * The price_types that belong to the Distributor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function price_types(): BelongsToMany
    {
        return $this->belongsToMany(PriceType::class);
    }

    /**
     * Get the zone that owns the Distributor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'distributor_id');
    }

    // default save username
    protected static function booted()
    {
        static::creating(function ($model)  {
            $model->username = Auth::user()->name;


        });
    }

    // get active record
    function scopeStatus($query)
    {
       return $query->where('status',1);
    }

    public function Route()
    {
        return $this->hasMany(Route::class);
    }

    /**
     * Get all of the comments for the Distributor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Shop()
    {
        return $this->hasMany(Shop::class);
    }

    function scopeUniqueNo($query)
    {
     $id = $query->max('id')+1;
    return  $number = sprintf('%03d',$id);

    }

    function scopeSort($query)
    {
   return $query->orderBy('level1','ASC')
      ->orderBy('level2','ASC')
      ->orderBy('level3','ASC');

    }


        public function Users(): BelongsToMany
        {
            return $this->belongsToMany(User::class, 'users_distributors', 'distributor_id', 'user_id');
        }
        public function tso()
        {
            return $this->hasMany(TSO::class);
        }

        public function UserDistributor()
        {
            return $this->hasMany(UsersDistributors::class);
        }

}
