<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    for ($i = 0; $i < 1000; $i++){
        $this->comment(Inspiring::quote());
        sleep(10);
    }
})->describe('Display an inspiring quote');
