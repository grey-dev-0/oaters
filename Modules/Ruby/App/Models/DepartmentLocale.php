<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentLocale extends Model{
    use HasFactory;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'r_department_locales';

    /**
     * @inheritdoc
     */
    protected $guarded = [];
}
