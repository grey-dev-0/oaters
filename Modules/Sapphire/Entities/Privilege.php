<?php

namespace Modules\Sapphire\Entities;

use App\Traits\UsesTenantDatabase;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model{
    use Translatable, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 's_privileges';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['title'];

    public function roles(){
        return $this->belongsToMany(Role::class, 's_role_privileges');
    }
}
