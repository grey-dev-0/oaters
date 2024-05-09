<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Department;
use Spatie\Permission\Traits\HasRoles;

class Contact extends Model{
    use HasFactory, HasRoles;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_contacts';

    /**
     * @var string[] $guard_name Authentication guards supported.
     */
    protected array $guard_name = ['web', 'api'];

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

    public function phones(){
        return $this->hasMany(Phone::class);
    }

    public function emails(){
        return $this->hasMany(Email::class);
    }

    public function managed_departments(){
        return $this->belongsToMany(Department::class, 'r_subordinates', 'manager_id', 'department_id');
    }

    public function departments(){
        $this->appends[] = 'department';
        return $this->belongsToMany(Department::class, 'r_subordinates', 'contact_id', 'department_id');
    }

    public function superiors(){
        return $this->belongsToMany(self::class, 'r_subordinates', 'contact_id', 'manager_id');
    }

    public function subordinates(){
        return $this->belongsToMany(self::class, 'r_subordinates', 'manager_id', 'contact_id');
    }

    public function applicant(){
        return $this->hasOne(Applicant::class, 'id');
    }

    public function getDepartmentAttribute(){
        return $this->departments->first();
    }

    /**
     * Attaches localized roles of fetched contacts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string[] $roleColumns Columns to select from the main roles table.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRoles($query, $roleColumns = ['id']){
        return $query->with([
            'roles:'.implode(',', $roleColumns),
            'roles.translations' => fn($locales) => $locales->whereLocale(app()->getLocale())
        ]);
    }

    /**
     * Attaches default phone and email to fetched contacts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDefaultInfo($query){
        return $query->with([
            'phones' => fn($phones) => $phones->select(['id', 'contact_id', 'number'])->whereDefault(true),
            'emails' => fn($emails) => $emails->select(['id', 'contact_id', 'address'])->whereDefault(true)
        ]);
    }

    /**
     * Gets only actively hired contacts.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveRecruit($query){
        return $query->whereHas('applicant', fn($applicant) => $applicant->whereNotNull('recruited_at'));
    }
}
