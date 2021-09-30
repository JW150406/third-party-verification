<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
         'incoming-call',
         'assignment',
         'recordingstatus',
         'create-task',
         'accept-reservation',
         'enqueue-call',
         'token',
         'newcall',
         'agent_answer',
         'conference/*',
         '/*/token' ,
         '/*/client/agents',
         '/*/client/agentsales',
         '/*/client/formscript',
         'activityupdate',
         'checkactive',
         'ajax/*',
         'logout',
         'twilio/*',
         'callbacks/*',
         'admin/dashboard/*'

    ];
}
