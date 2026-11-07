<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'lc_settings';

    /**
     * {@inheritdoc}
     */
    protected $guarded = ['id'];
}
