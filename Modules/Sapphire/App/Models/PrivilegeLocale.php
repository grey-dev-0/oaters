<?php

namespace Modules\Sapphire\App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivilegeLocale extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 's_privilege_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = [];
}
