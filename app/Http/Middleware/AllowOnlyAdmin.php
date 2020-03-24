<?php

namespace App\Http\Middleware;

use Closure;

class AllowOnlyAdmin
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
        if (session()->has('admin') === false) {
            return redirect('/admin');
        }

        return $next($request);
    }
}
