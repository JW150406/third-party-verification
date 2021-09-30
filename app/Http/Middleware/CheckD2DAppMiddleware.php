<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckD2DAppMiddleware
{

     /**
     * for d2d app settings is on or off.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $clientId = Auth::user()->client_id;
        if(!isOnSettings($clientId,'is_enable_d2d_app')) {
            Auth::user()->token()->revoke();
            return response()->json([
                'status' => 'error',
                'message' => 'D2D app settings is switch off. Please contact your administrator for assisstance.'
            ], 400);
        }
        return $next($request);
    }
}
