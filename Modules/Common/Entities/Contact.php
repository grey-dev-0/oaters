<?php

namespace Modules\Common\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ruby\Entities\Applicant;
use Modules\Ruby\Entities\Department;

class Contact extends Model{
    use HasFactory, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_contacts';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    protected $hidden = ['departments'];

    /**
     * @inheritDoc
     */
    protected static function newFactory(){
        return \Modules\Common\Database\factories\ContactsFactory::new();
    }


    public function managed_departments(){
        return $this->belongsToMany(Department::class, 'r_subordinates', 'manager_id', 'department_id');
    }

    public function departments(){
        $this->appends[] = 'department';
        return $this->belongsToMany(Department::class, 'r_subordinates', 'client_id', 'department_id');
    }

    public function superiors(){
        return $this->belongsToMany(self::class, 'r_subordinates', 'client_id', 'manager_id');
    }

    public function subordinates(){
        return $this->belongsToMany(self::class, 'r_subordinates', 'manager_id', 'client_id');
    }

    public function applicant(){
        return $this->hasOne(Applicant::class, 'id');
    }

    public function getDepartmentAttribute(){
        return $this->departments->first();
    }
}
