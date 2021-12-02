<?php

namespace App\Http\Middleware;

use Bouncer;
use Closure;
use Illuminate\Http\Request;

class ability
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$role)
    {
        if (!Bouncer::can($role)) {
            return redirect('dashboard');
        }

        return $next($request);
    }
}
