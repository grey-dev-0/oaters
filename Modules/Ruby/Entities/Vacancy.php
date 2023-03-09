<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacancy extends Model{
    use HasFactory, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    protected $table = 'r_vacancies';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    public function applicants(){
        return $this->morphToMany(Applicant::class, 'applicable');
    }
}
