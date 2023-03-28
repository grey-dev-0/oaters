<?php

namespace Modules\Sapphire\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(){
        Gate::define('manage-tenants', fn($tenant) => $tenant->main);
        Gate::define('authorize', fn($user, $privilege) => $user->accesses($privilege));
    }
}
