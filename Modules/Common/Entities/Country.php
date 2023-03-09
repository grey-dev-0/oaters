<?php

namespace Modules\Common\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ruby\Entities\Applicant;

class Country extends Model{
    use HasFactory, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_countries';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    public function applicants(){
        return $this->hasMany(Applicant::class);
    }
}
