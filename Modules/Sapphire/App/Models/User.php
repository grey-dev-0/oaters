<?php

namespace Modules\Sapphire\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Common\App\Models\Contact;

class User extends Authenticatable{
    use HasFactory;

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
