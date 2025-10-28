<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\City;

class UserDetail extends Model
{
    use HasFactory;
    protected $table = "user_details";
    protected $guarded = [];


    /** Scopes */
    // get unique no
    function scopeUniqueNo($query)
    {
   //  $id = $query->max('id')+1;
 //    $number = sprintf('%03d',$id);
     $number =   DB::table($this->table)->max('user_code')+1;
     return  $number = sprintf('%03d',$number);

    }

    public function cities()
    {
        return $this->belongsTo(City::class , 'city');
    }

}
