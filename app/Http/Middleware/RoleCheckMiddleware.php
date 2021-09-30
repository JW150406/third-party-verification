<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RoleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->hasRole(config('constants.ROLE_CLIENT_ADMIN'))) {
            if (!$request->has('client_id') || $request->client_id == "") {
                $request->request->add(['client_id' => Auth::user()->client_id]);
            } else if ($request->has('client_id') && $request->client_id != Auth::user()->client_id) {
                return response()->json(['status' => 'error', 'message' => 'You can not access other clients data.']);
            }
        }

        return $next($request);
    }
}
