<?php

namespace Modules\Sapphire\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Common\Entities\Contact;

class User extends Model{
    use HasFactory, UsesTenantDatabase;

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
