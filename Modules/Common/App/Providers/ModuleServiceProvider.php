<?php

namespace Modules\Common\App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Common';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'common';

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
