<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class CustomUrlGenerator extends UrlGenerator
{
    /**
     * For get route from the given parameters
     * @param $name, $parameters, $expiration
     */
    public function signedRoute($name, $parameters = [], $expiration = null)
    {
    	
        $parameters = $this->formatParameters($parameters);

        if ($expiration) {
            $parameters = $parameters + ['expires' => $this->availableAt($expiration)];
        }

        ksort($parameters);

        $key = call_user_func($this->keyResolver);

        return $this->route($name, $parameters + [
            'signature' => hash_hmac('sha1', $this->route($name, $parameters), $key),
        ]);
    }

    /**
     * This method is used for valid signature
     */
    public function hasValidSignature(Request $request)
    {
        $original = rtrim($request->url().'?'.http_build_query(
            Arr::except($request->query(), 'signature')
        ), '?');

        $expires = Arr::get($request->query(), 'expires');

        $signature = hash_hmac('sha1', $original, call_user_func($this->keyResolver));

        return  hash_equals($signature, $request->query('signature', '')) &&
               ! ($expires && Carbon::now()->getTimestamp() > $expires);
    }
}
