<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\Contact;

class Leave extends Model{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $table = 'r_leaves';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * @inheritdoc
     */
    protected $casts = ['starts_at', 'ends_at'];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Ruby\Database\Factories\LeavesFactory::new();
    }

    public function contact(){
        return $this->belongsTo(Contact::class);
    }
}
