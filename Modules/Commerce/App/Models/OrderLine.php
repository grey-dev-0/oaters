<?php

namespace Modules\Commerce\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\MeasurementUnit;

class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Commerce\Database\factories\OrderLineFactory::new();
    }

    public function quantityUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'quantity_unit_id');
    }
}
