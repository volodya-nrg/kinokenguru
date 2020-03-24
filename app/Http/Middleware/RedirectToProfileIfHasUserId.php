<?php

namespace App\Http\Middleware;

use Closure;

class RedirectToProfileIfHasUserId
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
        if (session()->has('user_id')) {
            return redirect('/profile');
        }

        return $next($request);
    }
}
