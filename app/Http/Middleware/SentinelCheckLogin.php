<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Support\Facades\Redirect;

class SentinelCheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            if (Sentinel::check())
            {
                return $next($request);
            }
            return Redirect::to('login')->withErrors('Bạn phải đăng nhập vào hệ thống!');
        }catch (\Exception $e)
        {
            return Redirect::to('login')->withErrors('Tài khoản của bạn chưa được kích hoạt!');
        }
    }
}
