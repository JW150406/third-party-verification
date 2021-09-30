<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CriticalAlert extends Notification
{
    use Queueable;
    public $agent;
    public $lead;
    public $alerts;
    public $path;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($agent=null,$lead=null,$alerts=[],$path=null)
    {
        $this->agent = $agent;
        $this->lead = $lead;
        $this->alerts = $alerts ;
        $this->path = $path ;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $lead = $this->lead;
        $agent = $this->agent;
        if($lead->status =='cancel') {
            $status = '(current status - cancelled):';
        } else {
            $status = '(current status - '.$lead->status.'):';
        }
        
        $msg = "We’ve received the following alert(s) for the sales agent <b>{$agent->full_name}</b> (ID: {$agent->userid}) in regards to lead ID {$lead->refrence_id} {$status}";
        $alerts = '<ol>';
        foreach ($this->alerts as $key => $alert) {
            $alerts .="<li>$alert</li>";
        }
        $alerts .= '</ol>';
        $url = route('critical-logs.show', array_get($lead, 'id'));
        $msgEnd = "To view more information regarding this lead, please click <a href='{$url}'>here</a>.";

        $msg .= $alerts.$msgEnd;
        return (new MailMessage)
                ->greeting('Dear '.$notifiable->first_name.',')
                ->attach($this->path,[
                    'mime' => 'application/pdf',
                    ])
                ->markdown('emails.common', ['msg' => $msg]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
