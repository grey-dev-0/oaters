<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepartmentLocale extends Model{
    use HasFactory, UsesTenantDatabase;

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
