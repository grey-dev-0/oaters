<?php

namespace Modules\Common\App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Ruby\App\Models\Applicant;

class Country extends Model{
    use HasFactory, Translatable;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_countries';

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
        return \Modules\Common\Database\Factories\CountriesFactory::new();
    }

    public function applicants(){
        return $this->hasMany(Applicant::class);
    }
}
