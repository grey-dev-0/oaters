<?php

namespace Modules\Article\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory(){
        return \Modules\Article\Database\factories\ArticleFactory::new();
    }
}
