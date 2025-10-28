<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class PriceType extends Model
{
    use HasFactory;

    protected $fillable = ['price_type_name','status'];
    protected $primarykey = 'id';

    /**
     * The price_types that belong to the Distributor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function distributors(): BelongsToMany
    {
        return $this->belongsToMany(Distributor::class);
    }
    
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
}
