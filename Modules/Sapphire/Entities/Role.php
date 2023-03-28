<?php

namespace Modules\Sapphire\Entities;

use App\Traits\UsesTenantDatabase;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model{
    use Translatable, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 's_roles';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['title'];

    public function privileges(){
        return $this->belongsToMany(Privilege::class, 's_role_privileges');
    }
}
