<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model{
    use HasFactory;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_emails';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Common\Database\Factories\EmailsFactory::new();
    }
}
