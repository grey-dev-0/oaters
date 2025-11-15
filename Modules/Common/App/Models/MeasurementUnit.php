<?php

namespace Modules\Common\App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeasurementUnit extends Model{
    use HasFactory, Translatable, SoftDeletes;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_measurement_units';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['name', 'symbol'];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Common\Database\Factories\MeasurementUnitsFactory::new();
    }

    public function base(){
        return $this->belongsTo(self::class, 'base_id');
    }

    public function derivedUnits(){
        return $this->hasMany(self::class, 'base_id');
    }
}
