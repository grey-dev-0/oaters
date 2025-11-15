<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementUnitLocale extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_measurement_unit_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];
}
