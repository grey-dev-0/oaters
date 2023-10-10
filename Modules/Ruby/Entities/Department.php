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
     * Return head(s) of the department whom are not supervised by any other contact within the department.
     * Should be one head in most cases but, was left out for implementation simplicity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function head() {
        return $this->managers()->whereNotExists(fn($query) => $query->selectRaw(1)->from('r_subordinates AS rs')
            ->whereRaw('r_subordinates.department_id = rs.department_id AND lc_contacts.id = rs.contact_id'))
            ->groupBy('pivot_manager_id');
    }

    /**
     * A relationship that is used to fetch all staff members of a department including managers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff(){
        $this->appends[] = 'members';
        return $this->subordinates()->with(['managers', 'contacts']);
    }

    /**
     * Direct relationship with subordinates pivot table - to be used for quickly counting department members.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subordinates() {
        return $this->hasMany(Subordinate::class);
    }

    public function applicants(){
        return $this->morphToMany(Applicant::class, 'applicable');
    }

    public function getMembersAttribute(){
        return $this->staff->managers->merge($this->staff->contacts)->unique('id')->values();
    }
}
