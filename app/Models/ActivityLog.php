<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    // use HasFactory;

    public function Log()
    {
        return $this->morphTo('activity_logs', 'table_type', 'table_id');
    }
}
