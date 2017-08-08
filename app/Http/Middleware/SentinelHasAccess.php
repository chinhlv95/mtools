<?php
namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Support\Facades\Redirect;

class SentinelHasAccess {

     /**
   * Sentry - Check role permission
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
    public function handle($request, Closure $next)
    {
        $actions = $request->route()->getAction();
        if (array_key_exists('hasAccess', $actions))
        {
            $permissions = $actions['hasAccess'];
            $user = Sentinel::getUser();
            foreach ($permissions as $p)
            {
                if ($user->hasAccess($p)) {
                    return $next($request);
                }
            }
            return Redirect::back();
        }
        return $next($request);
    }
}
