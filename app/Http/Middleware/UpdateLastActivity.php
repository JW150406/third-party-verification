<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class UpdateLastActivity
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
        (new User)->updateUser(
            auth()->user()->id,
            array(
                'last_activity' => date('Y-m-d H:i:s')
            )
        );

        return $next($request);
    }
}
