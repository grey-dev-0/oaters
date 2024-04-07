<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model{
    use HasFactory;

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
