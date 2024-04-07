<?php

namespace Modules\Ruby\App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model{
    use HasFactory, Translatable;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'r_degrees';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['name'];

    public function applicants(){
        return $this->hasMany(Applicant::class);
    }
}
