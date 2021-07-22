<?php

namespace Modules\Sapphire\Entities;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model{
    protected $guarded = ['id'];

    public function modules(){
        return $this->belongsToMany(Module::class, 'tenant_modules', 'subscription_id', 'module_id');
    }
}
