<?php

namespace Modules\Sapphire\Constants;

use Modules\Sapphire\Entities\Role;

final class Roles{
    public const OWNER = 1;
    public const FINANCE_MANAGER = 2;
    public const HR_MANAGER = 3;
    public const DIRECTOR = 4;
    public const LEAD = 5;
    public const ACCOUNTANT = 6;
    public const HR_AGENT = 7;
    public const EMPLOYEE = 8;
    public const SUPPLIER = 9;
    public const CUSTOMER = 10;

    /**
     * System roles list.
     *
     * @return int[]
     */
    private static function roles(){
        return [
            self::OWNER,
            self::FINANCE_MANAGER,
            self::HR_MANAGER,
            self::DIRECTOR,
            self::LEAD,
            self::ACCOUNTANT,
            self::HR_AGENT,
            self::EMPLOYEE,
            self::SUPPLIER,
            self::CUSTOMER,
        ];
    }

    /**
     * Gets localized system roles keyed by their IDs.
     *
     * @return array
     */
    public static function options(){
        $roles = Role::whereIn('id', self::roles())->with(['translations' =>
            fn($locale) => $locale->whereLocale(\App::getLocale())])->get();
        return $roles->pluck('title', 'id')->toArray();
    }

    /**
     * Determines whether the given role is a system role or a user-defined one.
     *
     * @param int $role The ID of the role to inspect.
     * @return bool
     */
    public static function inSystemRoles($role){
        return in_array($role, self::roles());
    }
}