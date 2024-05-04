<?php

namespace Modules\Sapphire\App\Models;

use Astrotomic\Translatable\Translatable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole{
    use Translatable;

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['title'];
}
