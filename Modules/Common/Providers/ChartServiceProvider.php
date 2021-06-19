<?php

namespace Modules\Common\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Modules\Common\Services\Chart;

class ChartServiceProvider extends ServiceProvider implements DeferrableProvider{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton('chart', function(){
            return new Chart();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['chart'];
    }
}
