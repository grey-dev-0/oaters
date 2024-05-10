<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryLocale extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_country_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];
}
