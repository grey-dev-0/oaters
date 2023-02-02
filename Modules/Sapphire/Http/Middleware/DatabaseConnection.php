<?php

namespace Modules\Sapphire\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Sapphire\Entities\Tenant;

class DatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $subdomain = explode('.', $request->header('host'))[0]?? 'oaters';
        if(!in_array($subdomain, ['www', 'api', 'oaters']) && !is_null($tenant = Tenant::whereSubdomain($subdomain)->first())){
            \DB::purge('tenant');
            putenv("SUB_DB_DATABASE={$tenant->hash}_oaters", );
            \DB::reconnect('tenant');
        }
        return $next($request);
    }
}
