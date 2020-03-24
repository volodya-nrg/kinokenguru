<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Profiles;

class CheckMyCookie
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
        $cookie = $request->cookie('my_cookie');

        if (!empty($cookie) && empty(session()->has('user_id'))) {
            $ar = explode("|", $cookie);

            if (sizeof($ar) === 3) {
                $profile_id = (int)$ar[0];
                $secret = $ar[1];
                $time = (int)$ar[2];

                // проверим по времени
                if ($time > time() && !empty($secret) && !empty($profile_id)) {
                    $count = Profiles::where([
                        ['id', '=', $profile_id],
                        ['key_cookie', '=', $secret]
                    ])->count();

                    if ($count === 1) {
                        session(['user_id' => $profile_id]);
                    }
                }
            }
        }

        return $next($request);
    }
}
