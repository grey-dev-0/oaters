<?php

namespace Modules\Common\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model{
    use UsesTenantDatabase;

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
