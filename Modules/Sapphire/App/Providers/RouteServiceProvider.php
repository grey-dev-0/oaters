<?php

namespace Modules\Sapphire\App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class RouteServiceProvider extends ServiceProvider{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Sapphire\App\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot(){
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(){
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(){
        Route::middleware('web')->prefix('sa')
            ->namespace("{$this->moduleNamespace}\\Admin")
            ->group(module_path('Sapphire', '/routes/admin.php'));

        Route::middleware('web')->prefix('st')
            ->namespace("{$this->moduleNamespace}\\Tenant")
            ->group(module_path('Sapphire', '/routes/tenant.php'));

        Route::middleware([
            'web',
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ])->prefix('s')
            ->namespace("{$this->moduleNamespace}\\User")
            ->group(module_path('Sapphire', '/routes/user.php'));
    }
}
