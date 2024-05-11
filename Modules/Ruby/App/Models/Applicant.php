<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Country;

class Applicant extends Model{
    use HasFactory;

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
    protected $guarded = ['id'];

    /**
     * @inheritdoc
     */
    protected $casts = ['recruited_at' => 'datetime'];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Ruby\Database\Factories\ApplicantsFactory::new();
    }

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
