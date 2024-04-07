<?php

namespace Modules\Article\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory(){
        return \Modules\Article\Database\factories\PropertyFactory::new();
    }
}
