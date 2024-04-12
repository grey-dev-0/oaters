<?php

namespace Modules\Sapphire\App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
    implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, TenantWithDatabase{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasDatabase, HasDomains, HasRoles;

    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public static function getCustomColumns(): array{
        return ['id', 'user_id', 'name', 'email', 'password', 'hash', 'created_at', 'updated_at'];
    }

    public function getIncrementing(){
        return true;
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}
