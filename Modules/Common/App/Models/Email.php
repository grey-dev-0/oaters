<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model{

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
