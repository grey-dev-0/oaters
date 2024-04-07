<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_phones';

    /**
     * @inheritdoc
     */
    protected $guarded = [];
}
