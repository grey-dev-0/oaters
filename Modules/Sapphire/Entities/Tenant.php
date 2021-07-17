<?php

namespace Modules\Sapphire\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Tenant extends Authenticatable
{
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}
