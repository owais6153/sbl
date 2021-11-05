<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class AjaxRequestValidation
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
        if (Auth::user() ) {
            return $next($request);
        }
        else{
          return response()->json(["error" => 'Please logout and log back in.', 'status' => 'error']);
        }
    }
}
