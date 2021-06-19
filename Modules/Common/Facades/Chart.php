<?php namespace Modules\Common\Facades;

use Illuminate\Support\Facades\Facade;

class Chart extends Facade{
    protected static function getFacadeAccessor(){
        return 'chart';
    }
}