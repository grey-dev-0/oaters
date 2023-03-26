<?php

namespace Modules\Common\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;

class Email extends Model{
    use UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_emails';

    /**
     * @inheritdoc
     */
    protected $guarded = [];
}
