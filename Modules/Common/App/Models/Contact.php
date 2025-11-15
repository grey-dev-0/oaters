<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\Leave;
use Modules\Ruby\App\Models\Shift;
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
     * @inheritDoc
     */
    protected static function newFactory(){
        return \Modules\Common\Database\Factories\ContactsFactory::new();
    }

    public function phones(){
        return $this->hasMany(Phone::class);
    }

    public function emails(){
        return $this->hasMany(Email::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }

    public function managed_departments(){
        return $this->belongsToMany(Department::class, 'r_subordinates', 'manager_id', 'department_id')
            ->groupBy('department_id');
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

    public function leaves(){
        return $this->hasMany(Leave::class);
    }

    public function shifts(){
        return $this->belongsToMany(Shift::class, 'r_contact_shifts', 'contact_id', 'shift_id')->withPivot('weekday');
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
     * Attaches all phones, emails and, addresses to fetched contacts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllInfo($query){
        $localeFilter = fn($locales) => $locales->whereLocale(app()->getLocale());
        return $query->with(['phones', 'emails', 'addresses.city:id,country_id', 'addresses.city.translations' => $localeFilter,
            'addresses.city.country:id', 'addresses.city.country.translations' => $localeFilter]);
    }

    /**
     * Attaches all recruitment related details to fetched contacts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRecruitmentDetails($query){
        return $query->with([
            'applicant:id,country_id,degree_id,degree_date,tenure,recruited_at', 'applicant.nationality:id',
            'applicant.nationality.translations' => fn($locales) => $locales->whereLocale(app()->getLocale()),
            'applicant.degree.translations' => fn($locales) => $locales->whereLocale(app()->getLocale()),
            'applicant.documents'
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

    /**
     * Gets all departments related to the contact.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDepartments($query){
        $locale = app()->getLocale();
        return $query->with([
            'departments.translations' => fn($locales) => $locales->whereLocale($locale),
            'managed_departments.translations' => fn($locales) => $locales->whereLocale($locale)
        ]);
    }
}
