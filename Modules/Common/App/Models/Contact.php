<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\Leave;
use Modules\Ruby\App\Models\Punch;
use Modules\Ruby\App\Models\Shift;
use Modules\Ruby\App\Models\Subordinate;
use Spatie\Permission\Traits\HasRoles;

class Contact extends Model
{
    use HasFactory, HasRoles, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $table = 'lc_contacts';

    /**
     * @var string[] Authentication guards supported.
     */
    protected array $guard_name = ['web', 'api'];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritDoc}
     */
    protected static function newFactory()
    {
        return \Modules\Common\Database\Factories\ContactsFactory::new();
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function managed_departments()
    {
        return $this->belongsToMany(Department::class, 'r_subordinates', 'manager_id', 'department_id')
            ->groupBy('department_id');
    }

    public function departments()
    {
        $this->appends[] = 'department';

        return $this->belongsToMany(Department::class, 'r_subordinates', 'contact_id', 'department_id');
    }

    public function superiors()
    {
        return $this->belongsToMany(self::class, 'r_subordinates', 'contact_id', 'manager_id');
    }

    public function subordinates()
    {
        return $this->belongsToMany(self::class, 'r_subordinates', 'manager_id', 'contact_id');
    }

    public function applicant()
    {
        return $this->hasOne(Applicant::class, 'id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function punches()
    {
        return $this->hasMany(Punch::class);
    }

    public function memberships()
    {
        return $this->hasMany(Subordinate::class, 'contact_id');
    }

    public function user()
    {
        return $this->belongsTo(\Modules\Sapphire\App\Models\User::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'r_contact_shifts', 'contact_id', 'shift_id')->withPivot('weekday');
    }

    public function getDepartmentAttribute()
    {
        return $this->departments->first();
    }

    /**
     * Gets the streamed profile image URL of the contact, when present.
     *
     * @return ?string
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? url('r/contacts/image/'.$this->image) : null;
    }

    /**
     * Attaches localized roles of fetched contacts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string[]  $roleColumns  Columns to select from the main roles table.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRoles($query, $roleColumns = ['id'])
    {
        return $query->with([
            'roles:'.implode(',', $roleColumns),
            'roles.translations' => fn ($locales) => $locales->whereIn('locale', [app()->getLocale(), static::fallbackLocale()]),
        ]);
    }

    /**
     * Attaches default phone and email to fetched contacts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDefaultInfo($query)
    {
        return $query->with([
            'phones' => fn ($phones) => $phones->select(['id', 'contact_id', 'number'])->whereDefault(true),
            'emails' => fn ($emails) => $emails->select(['id', 'contact_id', 'address'])->whereDefault(true),
        ]);
    }

    /**
     * Attaches all phones, emails and, addresses to fetched contacts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllInfo($query)
    {
        $locales = [app()->getLocale(), static::fallbackLocale()];
        $localeFilter = fn ($translations) => $translations->whereIn('locale', $locales);

        return $query->with(['phones', 'emails', 'addresses.city:id,country_id', 'addresses.city.translations' => $localeFilter,
            'addresses.city.country:id', 'addresses.city.country.translations' => $localeFilter]);
    }

    /**
     * Attaches all recruitment related details to fetched contacts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRecruitmentDetails($query)
    {
        $locales = [app()->getLocale(), static::fallbackLocale()];

        return $query->with([
            'applicant:id,country_id,degree_id,degree_date,tenure,recruited_at', 'applicant.nationality:id',
            'applicant.nationality.translations' => fn ($translations) => $translations->whereIn('locale', $locales),
            'applicant.degree.translations' => fn ($translations) => $translations->whereIn('locale', $locales),
            'applicant.documents',
        ]);
    }

    /**
     * Gets only actively hired contacts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveRecruit($query)
    {
        return $query->whereHas('applicant', fn ($applicant) => $applicant->whereNotNull('recruited_at'));
    }

    /**
     * Gets all departments related to the contact.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDepartments($query)
    {
        $locales = [app()->getLocale(), static::fallbackLocale()];

        return $query->with([
            'departments.translations' => fn ($translations) => $translations->whereIn('locale', $locales),
            'managed_departments.translations' => fn ($translations) => $translations->whereIn('locale', $locales),
        ]);
    }

    /**
     * The locale whose values are shown when a record has no translation
     * for the active one (spec 003 FR-003).
     */
    public static function fallbackLocale(): string
    {
        return config('translatable.fallback_locale') ?: config('app.fallback_locale');
    }
}
