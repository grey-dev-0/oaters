<?php

namespace Modules\Sapphire\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;

class RoleLocale extends Model{
    use UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 's_role_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = [];
}
