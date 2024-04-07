<?php

namespace Modules\Emerald\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSchedule extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Emerald\Database\factories\CustomScheduleFactory::new();
    }
}
