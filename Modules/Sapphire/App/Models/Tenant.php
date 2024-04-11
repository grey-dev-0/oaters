<?php

namespace Modules\Sapphire\App\Models;

use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase{
    use HasDatabase, HasDomains, HasRoles;

    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public static function getCustomColumns(): array{
        return ['id', 'user_id', 'name', 'email', 'password', 'hash'];
    }

    public function getIncrementing(){
        return true;
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}
