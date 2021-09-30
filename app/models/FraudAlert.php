<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FraudAlert extends Model
{
    protected $table = "fraud_alerts";

    protected $fillable = ['email', 'phone', 'alert_level', 'client_id', 'salescenter_id', 'location_id', 'added_by', 'added_for_client', 'type','alert_for'];

    /* Retrieve email list according to given client, sales center and location id */
    public function getDispositionEmailList($clientId, $scId = "", $locId = "") {
        $query = $this->select('email')->whereRaw('FIND_IN_SET("disposition", alert_for)')
                ->whereNotNull('email')
                ->where(function($q) use($clientId, $scId, $locId){
                    $q->where('client_id', $clientId);
                    if ($scId != "") {
                        $q->orWhereRaw('FIND_IN_SET("' . $scId . '", salescenter_id)');
                    }
                    if ($locId != "") {
                        $q->orWhereRaw('FIND_IN_SET("' . $locId . '", location_id)');
                    }
                });
        return $query->get();
    }
}
