<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Shift;

class Punch extends Model{
    /**
     * @inheritdoc
     */
    protected $table = 'r_punches';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    public function contact(){
        return $this->belongsTo(Contact::class);
    }

    public function shift(){
        return $this->belongsTo(Shift::class);
    }
}
