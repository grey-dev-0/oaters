<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\Contact;

class Shift extends Model{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'r_shifts';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    public function contacts(){
        return $this->belongsToMany(Contact::class, 'r_contact_shifts', 'shift_id', 'contact_id')->withPivot('weekday');
    }
}
