<?php

namespace App\Providers;

use App\Services\Chart;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ChartServiceProvider extends ServiceProvider implements DeferrableProvider{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(){
        $this->app->bind('chart-js', function(){
            return new Chart();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(){
        //
    }

    /**
     * @inheritDoc
     */
    public function provides(){
        return ['chart-js'];
    }
}
