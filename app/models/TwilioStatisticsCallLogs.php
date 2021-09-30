<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsCallLogs extends Model
{
    protected $table = 'twilio_statistics_call_logs';
    protected $fillable = ['account_sid',
    'annotation',
    'answered_by',
    'caller_name',
    'direction',
    'duration',
    'end_time',
    'forwarded_from',
    'from',
    'from_formatted',
    'group_sid',
    'parent_call_sid',
    'phone_number_sid',
    'price',
    'price_unit',
    'call_sid',
    'start_time',
    'status',
    'subresource_uris_recordings',
    'to_formatted',
    'to'];
    public function saveTwilioCallLogs($twilioDate,$val,$update = false)
    {
        if($update == true)
        {
            $twilioCallData = TwilioStatisticsCallLogs::where('call_sid',$val->sid)->where('created_at','>=',$twilioDate.' 00:00:00')->first();
            $twilioCalls = $twilioCallData;
        }
        else
            $twilioCalls = new TwilioStatisticsCallLogs();
        $twilioCalls->account_sid = $val->accountSid;
        $twilioCalls->annotation = $val->annotation;
        $twilioCalls->answered_by = $val->answeredBy;
        $twilioCalls->caller_name = $val->callerName;
        $twilioCalls->direction = $val->direction;
        $twilioCalls->duration = $val->duration;
        $twilioCalls->end_time = $val->endTime;
        $twilioCalls->forwarded_from = $val->forwardedFrom;
        $twilioCalls->from = $val->from;
        $twilioCalls->from_formatted = $val->fromFormatted;
        $twilioCalls->group_sid = $val->groupSid;
        $twilioCalls->parent_call_sid = $val->parentCallSid;
        $twilioCalls->phone_number_sid = $val->phoneNumberSid;
        $twilioCalls->price = $val->price;
        $twilioCalls->price_unit = $val->priceUnit;
        $twilioCalls->call_sid = $val->sid;
        $twilioCalls->start_time = $val->startTime;
        $twilioCalls->status = $val->status;
        $twilioCalls->subresource_uris_recordings = $val->subresourceUris['recordings'];
        $twilioCalls->to_formatted = $val->toFormatted;
        $twilioCalls->to = $val->to;
        $twilioCalls->save();
    }
}
