<?php

namespace Modules\Common\App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model{
    use HasFactory, Translatable;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_cities';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['name'];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Common\Database\Factories\CitiesFactory::new();
    }
}
