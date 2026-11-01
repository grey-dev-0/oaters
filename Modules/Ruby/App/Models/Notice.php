<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $table = 'r_notices';

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Ruby\Database\factories\NoticeFactory::new();
    }
}
