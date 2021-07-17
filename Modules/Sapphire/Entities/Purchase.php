<?php

namespace Modules\Sapphire\Entities;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model{
    protected $guarded = ['id'];

    public function subscription(){
        return $this->belongsTo(Subscription::class);
    }
}
