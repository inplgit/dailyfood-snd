<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SlabCategoryDetail;
use Auth;
class SlabCategory extends Model
{
    use HasFactory;
    protected $table = 'slab_categories';
    protected $guarded = [];
    protected $primarykey = 'id';

    protected $casts = [
        'channel_ids' => 'array'
    ];

     // default save username
     protected static function booted()
     {
         static::creating(function ($model) {
             $model->username = Auth::user()->name;
             $model->date = date('Y-m-d');
         });
     }
     public function SlabCategoryDetail()
     {
         return $this->hasMany(SlabCategoryDetail::class,'slab_categories_id');
     }
     function scopeStatus($query)
    {
    return $query->where('slab_categories.status',1);
    }

    function scopeActive($query)
    {
    return $query->where('slab_categories.active',1);
    }
}
