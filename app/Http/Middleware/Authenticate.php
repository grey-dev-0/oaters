<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|void
     */
    protected function redirectTo($request){
        if(!$request->expectsJson()){
            return route(match($request->segment(1)){
                'o', => 'onyx-login',
                'a' => 'amethyst-login',
                't' => 'topaz-login',
                'e' => 'emerald-login',
                'r', => 'ruby::login',
                'sa', 'st' => 'admin-login',
                default => 'login'
            });
        }
    }
}
