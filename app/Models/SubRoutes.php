<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubRoutes extends Model
{
    use HasFactory;
    public $table ='sub_routes';
    protected $fillable = ['route_id','name','username'];
    protected $primarykey = 'id';


    public function route()
    {
        return $this->belongsTo(Route::class,'route_id');
    }
    function scopeStatus($query)
    {
       return $query->where('status',1);
    }
}
