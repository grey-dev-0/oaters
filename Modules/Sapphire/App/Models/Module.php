<?php

namespace Modules\Sapphire\App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function subscriptions(){
        return $this->belongsToMany(Subscription::class, 'tenant_modules', 'module_id', 'subscription_id');
    }
}
