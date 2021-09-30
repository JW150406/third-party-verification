<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\Telesales;

class DisconnectLeadUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lead;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Telesales $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lead = $this->lead;

        if (array_get($lead, 'status') == config('constants.LEAD_TYPE_PENDING')) {
            $lead->status = config('constants.LEAD_TYPE_DISCONNECTED');
            $lead->save();
            \Log::info("Lead registered as disconnected lead with id: " . array_get($lead, 'id'));
        } else {
            \Log::error("Lead is not in pending state. So can not register this lead as disconnected lead. Lead id: " . $lead->id);
            return false;
        }
    }
}
