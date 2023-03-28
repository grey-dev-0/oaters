<?php

namespace Modules\Sapphire\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;

class PrivilegeLocale extends Model{
    use UsesTenantDatabase;

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
