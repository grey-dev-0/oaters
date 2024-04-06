<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DataTablesHelper extends Facade{
    protected static function getFacadeAccessor(){
        return 'dt-helper';
    }
}