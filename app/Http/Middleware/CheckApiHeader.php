<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$response)
    {
        
        if($request->header('api-key') == config('constants.api_key'))
        {
            return $next($request);
        }
        return response()->json([
                    'status' => 'error',
                    'message' => "Invalid API key"
                ], 500);

    }
}
