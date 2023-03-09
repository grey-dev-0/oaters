<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Common\Entities\Contact;

class Department extends Model{
    use HasFactory, Translatable, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    protected $table = 'r_departments';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    protected $hidden = ['staff'];

    /**
     * @var string[] $translatedAttributes Attributes translated in the related localization table.
     */
    public $translatedAttributes = ['name'];

    public function managers(){
        return $this->belongsToMany(Contact::class, 'r_subordinates', 'department_id', 'manager_id');
    }

    public function employees(){
        return $this->belongsToMany(Contact::class, 'r_subordinates', 'department_id', 'contact_id');
    }

    /**
     * A relationship that is used to fetch all staff members of a department including managers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff(){
        $this->appends[] = 'members';
        return $this->hasMany(Subordinate::class)->with(['managers', 'contacts']);
    }

    public function applicants(){
        return $this->morphToMany(Applicant::class, 'applicable');
    }

    public function getMembersAttribute(){
        return $this->staff->managers->merge($this->staff->contacts);
    }
}
