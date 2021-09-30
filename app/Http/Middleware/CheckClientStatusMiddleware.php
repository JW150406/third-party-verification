<?php

namespace App\Http\Middleware;

use Closure;
use App\models\Client;

class CheckClientStatusMiddleware
{
    /**
     * Handle an incoming request.
     * for check client is active or not
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {        
        $clientId = (!empty($request->client_id)) ? $request->client_id : $request->client;
        
        $client = Client::find($clientId);
        if (empty($client)) {
            return response()->json(['status' => 'error', 'message' => 'Client not found.']);
        } else if (!empty($client) && !$client->isActive()) {
            return response()->json(['status' => 'error', 'message' => "You can't change anything due to client is inactive."]);
        }
        return $next($request);
    }
}
