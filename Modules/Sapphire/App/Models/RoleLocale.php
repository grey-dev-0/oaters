<?php

namespace Modules\Sapphire\App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleLocale extends Model{
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

    public function role(){
        return $this->belongsTo(Role::class);
    }
}
