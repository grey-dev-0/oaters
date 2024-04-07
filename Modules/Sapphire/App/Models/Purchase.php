<?php

namespace Modules\Sapphire\App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model{
    protected $guarded = ['id'];

    public function subscription(){
        return $this->belongsTo(Subscription::class);
    }
}
