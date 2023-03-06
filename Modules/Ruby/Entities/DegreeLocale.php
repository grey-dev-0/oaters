<?php

namespace Modules\Ruby\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DegreeLocale extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Ruby\Database\factories\DegreeLocaleFactory::new();
    }
}
