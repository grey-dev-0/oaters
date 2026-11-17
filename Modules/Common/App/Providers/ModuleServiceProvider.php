<?php

namespace Modules\Common\App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Modules\Common\App\Services\Settings;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Common';

    /**
     * @var string
     */
    protected $moduleNameLower = 'common';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Settings::class);

        AliasLoader::getInstance()->alias('Settings', \Modules\Common\App\Facades\Settings::class);
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerViews();
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewsPath = module_path($this->moduleName, 'resources/views');
        $this->loadViewsFrom([$viewsPath], $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
    }
}
