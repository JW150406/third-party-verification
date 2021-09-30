<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailBeingSent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
      // This adds the header of sender in the Mailgun
      // That eliminates the issue of the Outlook showing on behalf of
      // Ref: https://stackoverflow.com/a/36500003
       $headers = $event->message->getHeaders();
       $fromEmail = array_key_first($event->message->getFrom());
       if( $fromEmail != NULL )
       {
          $headers->addTextHeader('sender', $fromEmail);
       }
    }
}
