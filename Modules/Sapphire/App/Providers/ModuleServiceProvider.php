<?php

namespace Modules\Sapphire\App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Sapphire\App\Services\AccountLocalePreference;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Sapphire';

    /**
     * @var string
     */
    protected $moduleNameLower = 'sapphire';

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
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(\Modules\Common\App\Contracts\LocalePreference::class, AccountLocalePreference::class);
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
