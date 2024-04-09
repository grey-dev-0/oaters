<?php

namespace Modules\Sapphire\App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase{
    use HasDomains, HasDatabase;

    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function getIncrementing(){
        return true;
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}
