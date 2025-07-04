<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('admin')->check()) {
            return redirect(route('dashboard.index'));
        } elseif (auth('mini_admin')->check()) {
            return redirect(route('dashboard.miniadmin'));
        }

        return $next($request);
    }
}
