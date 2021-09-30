<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'client_id',
		'label_custom_field_1',
		'is_enable_field_1',
		'label_custom_field_2',
		'is_enable_field_2',
		'label_custom_field_3',
		'is_enable_field_3',
		'label_custom_field_4',
		'is_enable_field_4',
		'label_custom_field_5',
		'is_enable_field_5',
        'is_enable_d2d_app',
        'is_enable_ivr',
        'is_enable_cust_call_num',
        'is_enable_agent_tpv_num',
        'is_enable_outbound_tpv',
        'is_enable_self_tpv_tele',
        'restrict_states_self_tpv_tele',
        'is_enable_self_tpv_d2d',
        'restrict_states_self_tpv_d2d',
        'is_enable_contract_tele',
        'is_enable_contract_d2d',
        'is_enable_clone_lead',
        'is_enable_lead_view_page',
        'is_enable_lead_view_page_d2d',
        'is_enable_hunt_group',
        'is_enable_agent_time_clock',
        'is_enable_recording',
        'is_enable_alert_tele',
        'is_enable_alert1_tele',
        'is_enable_alert2_tele',
        'is_enable_alert3_tele',
        'is_enable_alert4_tele',
        'is_enable_alert5_tele',
        'is_enable_alert6_tele',
        'is_enable_alert7_tele',
        'is_enable_alert8_tele',
        'is_enable_alert9_tele',
        'is_enable_alert10_tele',
        'is_enable_alert11_tele',
        'is_enable_alert12_tele',
        'is_enable_alert_d2d',
        'is_enable_alert1_d2d',
        'is_enable_alert2_d2d',
        'is_enable_alert3_d2d',
        'is_enable_alert4_d2d',
        'is_enable_alert5_d2d',
        'is_enable_alert6_d2d',
        'is_enable_alert7_d2d',
        'is_enable_alert8_d2d',
        'is_enable_alert9_d2d',
        'is_enable_alert10_d2d',
        'is_enable_alert11_d2d',
        'is_enable_alert12_d2d',
        'is_enable_alert13_d2d',
        'is_critical_alert1_tele',
        'is_critical_alert2_tele',
        'is_critical_alert3_tele',
        'is_critical_alert4_tele',
        'is_critical_alert5_tele',
        'is_critical_alert6_tele',
        'is_critical_alert7_tele',
        'is_critical_alert8_tele',
        'is_critical_alert9_tele',
        'is_critical_alert10_tele',
        'is_critical_alert11_tele',
        'is_critical_alert12_tele',
        'is_critical_alert1_d2d',
        'is_critical_alert2_d2d',
        'is_critical_alert3_d2d',
        'is_critical_alert4_d2d',
        'is_critical_alert5_d2d',
        'is_critical_alert6_d2d',
        'is_critical_alert7_d2d',
        'is_critical_alert8_d2d',
        'is_critical_alert9_d2d',
        'is_critical_alert10_d2d',
        'is_critical_alert11_d2d',
        'is_critical_alert12_d2d',
        'is_critical_alert13_d2d',
        'is_show_agent_alert1_tele',
        'is_show_agent_alert2_tele',
        'is_show_agent_alert3_tele',
        'is_show_agent_alert4_tele',
        'is_show_agent_alert5_tele',
        'is_show_agent_alert6_tele',
        'is_show_agent_alert7_tele',
        'is_show_agent_alert8_tele',
        'is_show_agent_alert9_tele',
        'is_show_agent_alert10_tele',
        'is_show_agent_alert11_tele',
        'is_show_agent_alert12_tele',
        'is_show_agent_alert1_d2d',
        'is_show_agent_alert2_d2d',
        'is_show_agent_alert3_d2d',
        'is_show_agent_alert4_d2d',
        'is_show_agent_alert5_d2d',
        'is_show_agent_alert6_d2d',
        'is_show_agent_alert7_d2d',
        'is_show_agent_alert8_d2d',
        'is_show_agent_alert9_d2d',
        'is_show_agent_alert10_d2d',
        'is_show_agent_alert11_d2d',
        'is_show_agent_alert12_d2d',
        'is_show_agent_alert13_d2d',
        'max_times_alert1_tele',
        'max_times_alert3_tele',
        'max_times_alert4_tele',
        'max_times_alert10_tele',
        'max_times_alert1_d2d',
        'max_times_alert3_d2d',
        'max_times_alert4_d2d',
        'max_times_alert11_d2d',
        'interval_days_alert1_tele',
        'interval_days_alert2_tele',
        'interval_days_alert3_tele',
        'interval_days_alert4_tele',
        'interval_days_alert1_d2d',
        'interval_days_alert2_d2d',
        'interval_days_alert3_d2d',
        'interval_days_alert4_d2d',
        'is_enable_enroll_by_state',
        'is_enable_self_tpv_welcome_call',
        'self_tpv_max_no_of_call_attempt',
        'self_tpv_call_delay',
        'tpv_now_max_no_of_call_attempt',
		'tpv_now_call_delay',
        'is_enable_send_contract_after_lead_verify_tele',
        'is_enable_send_contract_after_lead_verify_d2d',
        'geomapping_radius',
        'lead_expiry_time',
        'is_outbound_disconnect',
        'outbound_disconnect_max_reschedule_call_attempt',
        'outbound_disconnect_schedule_call_delay',
        'interval_days_alert8_tele',
        'interval_days_alert9_tele',
        'interval_days_alert11_tele',
        'interval_days_alert12_tele',
        'interval_days_alert9_d2d',
        'interval_days_alert10_d2d',
        'interval_days_alert12_d2d',
        'interval_days_alert13_d2d'
    ];

    protected $table = 'settings';

    /**
     * for get enable custom field program by client id
     * @param $query
     * @param $clientId
     * @return array
     */
    public function scopeGetEnableFields($query,$clientId)
    {
    	$fields = $query->where('client_id', $clientId)->first();
    	$enableFields = [];

    	if (!empty($fields)) {
    		if ($fields->is_enable_field_1) {
    			$enableFields['custom_field_1'] = $fields->label_custom_field_1;
    		}
    		if ($fields->is_enable_field_2) {
    			$enableFields['custom_field_2'] = $fields->label_custom_field_2;
    		}
    		if ($fields->is_enable_field_3) {
    			$enableFields['custom_field_3'] = $fields->label_custom_field_3;
    		}
    		if ($fields->is_enable_field_4) {
    			$enableFields['custom_field_4'] = $fields->label_custom_field_4;
    		}
    		if ($fields->is_enable_field_5) {
    			$enableFields['custom_field_5'] = $fields->label_custom_field_5;
    		}
    	}
        return $enableFields;
    }

    /**
     * for get disable custom field program by client id
     * @param $query
     * @param $clientId
     * @return array
     */
    public function scopeGetDisableFields($query,$clientId)
    {
        $fields = $query->where('client_id', $clientId)->first();
        $disableFields = [];

        if (!empty($fields)) {
            if ($fields->is_enable_field_1 == 0) {
                $disableFields[] = 'custom_field_1';
            }
            if ($fields->is_enable_field_2 == 0) {
                $disableFields[] = 'custom_field_2';
            }
            if ($fields->is_enable_field_3 == 0) {
                $disableFields[] = 'custom_field_3';
            }
            if ($fields->is_enable_field_4 == 0) {
                $disableFields[] = 'custom_field_4';
            }
            if ($fields->is_enable_field_5 == 0) {
                $disableFields[] = 'custom_field_5';
            }
        }
        return $disableFields;
    }
}