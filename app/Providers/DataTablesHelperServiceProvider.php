<?php

namespace App\Providers;

use App\Services\DataTablesHelper;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DataTablesHelperServiceProvider extends ServiceProvider implements DeferrableProvider{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton('dt-helper', function(){
            return new DataTablesHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function provides(){
        return ['dt-helper'];
    }
}
