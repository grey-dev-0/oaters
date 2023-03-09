<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Degree extends Model{
    use HasFactory, Translatable, UsesTenantDatabase;

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
