<?php

namespace Modules\Sapphire\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authorize{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $privilege){
        if(\Gate::denies('authorize', $privilege))
            abort(401);
        return $next($request);
    }
}
