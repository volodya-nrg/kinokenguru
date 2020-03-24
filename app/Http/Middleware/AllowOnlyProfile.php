<?php

namespace App\Http\Middleware;

use Closure;

class AllowOnlyProfile
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (empty(session()->has('user_id'))) {
            abort(404);
        }

        return $next($request);
    }
}
