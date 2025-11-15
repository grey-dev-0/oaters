<?php

namespace Modules\Amethyst\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\MeasurementUnit;

class CartArticle extends Model{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory(){
        return \Modules\Amethyst\Database\factories\CartArticleFactory::new();
    }

    public function quantityUnit(){
        return $this->belongsTo(MeasurementUnit::class, 'quantity_unit_id');
    }
}
