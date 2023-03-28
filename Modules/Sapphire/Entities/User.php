<?php

namespace Modules\Sapphire\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Common\Entities\Contact;
use Modules\Sapphire\Traits\Authorizable;

class User extends Authenticatable{
    use Authorizable, HasFactory, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    protected $table = 's_users';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    protected static function newFactory(){
        return \Modules\Sapphire\Database\factories\UsersFactory::new();
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function contact(){
        return $this->hasOne(Contact::class);
    }
}
