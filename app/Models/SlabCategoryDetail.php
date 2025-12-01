<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SlabCategory;
use App\Models\Category;

class SlabCategoryDetail extends Model
{
    use HasFactory;
    protected $table = 'slab_categories_details';
    protected $guarded = [];
    protected $primarykey = 'id';

    public function SlabCategory()
    {
       return $this->belongsTo(SlabCategory::class,'slab_categories_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Category::class);
    }

    function scopeStatus($query)
    {
       return $query->where('status',1);
    }
}
