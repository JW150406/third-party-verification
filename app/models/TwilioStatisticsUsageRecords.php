<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsUsageRecords extends Model
{
    protected $table = 'twilio_statistics_usage_records';
    protected $fillable = ['account_sid',
    'page_size',
    'category',
    'count',
    'price_unit',
    'subresource_uris',
    'description',
    'end_date',
    'as_of',
    'usage_unit',
    'price',
    'usage',
    'start_date',
    'count_unit'];    

    public function saveTwilioUsageRecords($record,$data = '',$update = false)
    {
        if($update == true)
        {
            $twilioUsageRecord = $data;
        }
        else
            $twilioUsageRecord = new TwilioStatisticsUsageRecords();
        $twilioUsageRecord->account_sid = $record->accountSid;
        $twilioUsageRecord->category = $record->category;
        $twilioUsageRecord->count = $record->count;
        $twilioUsageRecord->price_unit = $record->priceUnit;
        $twilioUsageRecord->subresource_uris = $record->subresourceUris['today'];
        $twilioUsageRecord->description = $record->description;
        $twilioUsageRecord->end_date = $record->endDate;
        $twilioUsageRecord->as_of = $record->asOf;
        $twilioUsageRecord->usage_unit = $record->usageUnit;
        $twilioUsageRecord->price = $record->price;
        $twilioUsageRecord->usage = $record->usage;
        $twilioUsageRecord->start_date = $record->startDate;
        $twilioUsageRecord->count_unit = $record->countUnit;
        $twilioUsageRecord->save();
    }
}
