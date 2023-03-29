<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Illuminate\Http\Request;
use json;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()){
           // return json('unauthenticated user');
            return response()->json('unauthenticated user', 200);
        } // I included this check because you have it, but it really should be part of your 'auth' middleware, most likely added as part of a route group.
        
        $user = Auth::user();
        if($user->role == 0){
            return $next($request);
        }

        //return json('unauthorized user');
        return response()->json('unauthorized user', 200);
        
    }
}
