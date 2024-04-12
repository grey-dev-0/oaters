<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string{
        if(!$request->expectsJson())
            return route(match($request->segment(1)){
                'o', => 'onyx::login',
                'a' => 'amethyst::login',
                't' => 'topaz::login',
                'e' => 'emerald::login',
                'r', => 'ruby::login',
                'sa' => 'admin::login',
                'st' => 'tenant::login',
                's' => 'user::login',
                default => 'login'
            });
        return null;
    }
}
