<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Model;

class DegreeLocale extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'r_degree_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = [];
}
