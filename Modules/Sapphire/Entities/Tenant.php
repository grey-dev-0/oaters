<?php

namespace Modules\Sapphire\Entities;

use App\Traits\InitializesTenantDatabase;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tenant extends Authenticatable{
    use InitializesTenantDatabase;

    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}
