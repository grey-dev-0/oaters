<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Common\Entities\Contact;
use Modules\Common\Entities\Country;

class Applicant extends Model{
    use HasFactory, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    public $incrementing = false;

    /**
     * @inheritdoc
     */
    protected $table = 'r_applicants';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    public function documents(){
        return $this->hasMany(Document::class);
    }

    public function contact(){
        return $this->belongsTo(Contact::class, 'id');
    }

    public function nationality(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function degree(){
        return $this->belongsTo(Degree::class);
    }

    public function vacancies(){
        return $this->morphedByMany(Vacancy::class, 'applicable');
    }

    public function departments(){
        return $this->morphedByMany(Department::class, 'applicable');
    }
}
