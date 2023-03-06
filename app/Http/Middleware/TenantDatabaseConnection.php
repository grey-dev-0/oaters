<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantDatabaseConnection{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        if(!empty($subdomain = $request->subdomain) && $tenant = \Modules\Sapphire\Entities\Tenant::whereSubdomain($subdomain)->first())
            $tenant->connect();
        return $next($request);
    }
}
