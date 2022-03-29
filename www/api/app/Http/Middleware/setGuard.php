<?php
/**
 * Desc:
 * User: sukangshen@tal.com
 * Date: 2022/3/29
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class setGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($guard) Auth::shouldUse($guard);

        //dd(Auth::getDefaultDriver());  //as expected, outputs $guard if set, 'web' otherwise

        return $next($request);
    }
}
