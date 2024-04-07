<?php

namespace Modules\Sapphire\App\Providers\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Sapphire';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'sapphire';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(){
        $this->registerTranslations();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(){
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews(){
        $viewsPath = module_path($this->moduleName, 'resources/views');
        $this->loadViewsFrom([$viewsPath], $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(){
        $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
    }
}
