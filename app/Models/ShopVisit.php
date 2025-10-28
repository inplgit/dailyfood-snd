<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class ShopVisit extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'shop_id', 'visit_reason_id', 'merchandising_image','remark','visit_date','longitude','latitude','type','created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getMerchandisingImageAttribute($value)
    {
        // Check if the 'image' attribute is not empty
        if (!empty($value)) {
            // Generate and return the URL for the stored image
            return url('storage/app/public/visitshope/'.$value);
        }

        // If the 'image' attribute is empty, return a default or null value
        return '';
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function Route()
    {
        return $this->belongsTo(Route::class);
    }



    public function usersLocation()
    {
        return $this->morphMany(UsersLocation::class ,'table');
    }

}
