<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;

class CityLocale extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_city_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];
}
