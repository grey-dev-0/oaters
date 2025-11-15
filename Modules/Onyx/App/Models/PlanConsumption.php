<?php

namespace Modules\Onyx\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\MeasurementUnit;

class PlanConsumption extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Onyx\Database\factories\PlanConsumptionFactory::new();
    }

    public function quantityUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'quantity_unit_id');
    }
}
