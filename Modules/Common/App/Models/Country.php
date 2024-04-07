<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Ruby\App\Models\Applicant;

class Country extends Model{
    use HasFactory;

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
