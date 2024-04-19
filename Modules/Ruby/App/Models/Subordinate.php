<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subordinate extends Model{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $table = 'r_subordinates';

    /**
     * @inheritdoc
     */
    protected $fillable = [];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Ruby\Database\factories\SubordinateFactory::new();
    }
}
