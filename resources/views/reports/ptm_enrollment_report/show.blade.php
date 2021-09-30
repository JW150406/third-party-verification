@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(
    array('link' => 'javascript:void(0)', 'text' => 'Analytics'),
    array('link' => route('telesales.getLeads'), 'text' => 'Enrollment Report- PTM'),
    array('link' => "", 'text' => 'View Lead')
);
breadcrum($breadcrum);
?>
<style>
    .space-none {
        margin-top: 15px;
    }
    .cont_bx3 .pdlr0 {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>
    <div class="tpv-contbx edit-agentinfo">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="client-bg-white min-height-solve">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                                    <h1>Lead ID: <span>{{ $telesale->refrence_id}}</span></h1>
                                </div>
                            </div>
                        </div>
                        <div class="mt-15">
                            <div class="verify-label">
                                <?php
                                $status = config("constants.VERIFICATION_STATUS_CHART." . ucfirst($telesale->status));
                                $timeZone = Auth::user()->timezone;
                                ?>
                                {{$status}}
                            </div>
                            <div class="lead-update">
                                <p>Lead submission on: <span>{{\Carbon\Carbon::parse($telesale->created_at)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</span></p>
                            </div>
                            <div class="lead-update">
                                <p>Last updated on: <span>{{\Carbon\Carbon::parse($telesale->updated_at)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</span></p>
                            </div>
                        </div>
                        <div class="mt-15 agent-detail">
                            <div class="w-50 agent-info bg-white border-line min-height-solve">
                                <p><strong>Agent: </strong>{{ array_get($telesale, 'userWithTrashed') ? array_get($telesale->userWithTrashed, 'first_name') . " " . array_get($telesale->userWithTrashed, 'last_name') : "" }}</p>
                                <p><strong>Agent ID: </strong>{{ array_get($telesale, 'userWithTrashed') ? array_get($telesale->userWithTrashed, 'userid') : "" }}</p>
                                <p><strong>Email: </strong>{{ array_get($telesale, 'userWithTrashed') ? array_get($telesale->userWithTrashed, 'email') : "" }}</p>
                                @if((array_get($telesale, 'userWithTrashed') && array_get($telesale->userWithTrashed, 'salesAgentDetailsWithTrashed')) && array_get($telesale->userWithTrashed->salesAgentDetailsWithTrashed, 'phone_number'))
                                <p><strong>Phone: </strong> {{ (array_get($telesale, 'userWithTrashed') && array_get($telesale->userWithTrashed, 'salesAgentDetailsWithTrashed')) ? array_get($telesale->userWithTrashed->salesAgentDetailsWithTrashed, 'phone_number') : "" }}</p>
                                @endif
                            </div>
                            <div class="w-50 agent-info bg-white border-line min-height-solve">
                                    <p><strong>Client:</strong> @if(!empty($telesale->client))
                                        {{ $telesale->client->name}}
                                     @endif</p>
                                    <p><strong>Sales Center:</strong> @if(!empty($telesale->userWithTrashed) && !empty($telesale->userWithTrashed->salescenter))
                                        {{ $telesale->userWithTrashed->salescenter->name}}
                                     @endif</p>
                                    <p><strong>Sales Center Location:</strong> <?php if(!empty($telesale->userWithTrashed) && !empty($telesale->userWithTrashed->salesAgentDetailsWithTrashed) && !empty($telesale->userWithTrashed->salesAgentDetailsWithTrashed->location)) {
                                        echo ucfirst($telesale->userWithTrashed->salesAgentDetailsWithTrashed->location->name);
                                } ?></p>
                                    <p><strong>Channel:</strong> @if(!empty($telesale->userWithTrashed) && !empty($telesale->userWithTrashed->salesAgentDetailsWithTrashed))
                                        {{ strtoupper($telesale->userWithTrashed->salesAgentDetailsWithTrashed->agent_type)}}
                                     @endif</p>
                            </div>
                        </div>
                        <div class="mt-15 cust-info bg-white border-line min-height-solve">
                            <div class=" agent-info" style="padding: 0px !important;">
                                <h3>Personal Details</h3>
                                @foreach($lead_detail as $key => $lead)
                                    @if($lead['type'] == 'label')
                                        <?php continue; ?>
                                    @elseif($lead['type'] == 'fullname')
                                        <?php
                                        $fullName = [];
                                        foreach ($lead['telesales_data'] as $value) {
                                            if($value['meta_key'] != 'is_primary'){
                                                switch ($value['meta_key']) {
                                                    case 'first_name':
                                                    $fullName[0] = $value['meta_value'];
                                                    break;
                                                    case 'middle_initial':
                                                    $fullName[1] = $value['meta_value'];
                                                    break;
                                                    case 'last_name':
                                                    $fullName[2] = $value['meta_value'];
                                                    break;
                                                    default:
                                                    // code...
                                                    break;
                                                }
                                            }
                                        }
                                        // sort by key
                                        ksort($fullName);
                                        ?>
                                        <p><b>{{$lead['label']}}:</b> {{ implode(' ', $fullName) }}</p>
                                        @elseif($lead['type'] == 'address')
                                            <?php
                                            $address = [];
                                            foreach ($lead['telesales_data'] as $value) {
                                                $address[$value['meta_key']] = $value['meta_value'];
                                            }
                                            ?>
                                            <p><b>{{$lead['label']}}:</b></p>
                                            <p><?php
                                                if (isset($address['unit']) && !empty($address['unit'])) {
                                                    echo $address['unit'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['address_1']) && !empty($address['address_1'])) {
                                                    echo $address['address_1'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['address_2']) && !empty($address['address_2'])) {
                                                    echo $address['address_2'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['city']) && !empty($address['city'])) {
                                                    echo $address['city']. ', ';
                                                }
                                                // Concate address county
                                                if (isset($address['county']) && !empty($address['county'])) {
                                                    echo $address['county']. ', ';
                                                }
                                                // End
                                                if (isset($address['state']) && !empty($address['state'])) {
                                                    echo $address['state']. ', ';
                                                }
                                                if (isset($address['zipcode']) && !empty($address['zipcode'])) {
                                                    echo $address['zipcode'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['country']) && !empty($address['country'])) {
                                                    echo $address['country'];
                                                    echo '<br>';
                                                }
                                                ?>
                                            </p>
                                        @elseif($lead['type'] == 'service_and_billing_address')
                                            <?php
                                            $address = [];
                                            foreach ($lead['telesales_data'] as $value) {
                                                $address[$value['meta_key']] = $value['meta_value'];
                                            }
                                            ?>
                                            <p><b>Service Address:</b></p>
                                                <p><?php
                                                if (isset($address['service_unit']) && !empty($address['service_unit'])) {
                                                    echo $address['service_unit'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['service_address_1']) && !empty($address['service_address_1'])) {
                                                    echo $address['service_address_1'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['service_address_2']) && !empty($address['service_address_2'])) {
                                                    echo $address['service_address_2'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['service_city']) && !empty($address['service_city'])) {
                                                    echo $address['service_city'] . ', ';
                                                }
                                                // Concate service county
                                                if (isset($address['service_county']) && !empty($address['service_county'])) {
                                                    echo $address['service_county'] . ', ';
                                                }
                                                // End
                                                if (isset($address['service_state']) && !empty($address['service_state'])) {
                                                    echo $address['service_state'] . ', ';
                                                }
                                                if (isset($address['service_zipcode']) && !empty($address['service_zipcode'])) {
                                                    echo $address['service_zipcode'];
                                                    echo '<br>';
                                                }
                                                if (isset($address['service_country']) && !empty($address['service_country'])) {
                                                    echo $address['service_country'];
                                                    echo '<br>';
                                                }
                                                ?>
                                            </p>
                                            <p>
                                                <b>Billing Address:</b></p>
                                                <p><?php
                                                    if (isset($address['billing_unit']) && !empty($address['billing_unit'])) {
                                                        echo $address['billing_unit'];
                                                        echo '<br>';
                                                    }
                                                    if (isset($address['billing_address_1']) && !empty($address['billing_address_1'])) {
                                                        echo $address['billing_address_1'];
                                                        echo '<br>';
                                                    }
                                                    if (isset($address['billing_address_2']) && !empty($address['billing_address_2'])) {
                                                        echo $address['billing_address_2'];
                                                        echo '<br>';
                                                    }
                                                    if (isset($address['billing_city']) && !empty($address['billing_city'])) {
                                                        echo $address['billing_city'] . ', ';
                                                    }
                                                    // Concate billing county
                                                    if (isset($address['billing_county']) && !empty($address['billing_county'])) {
                                                        echo $address['billing_county'] . ', ';
                                                    }
                                                    // End
                                                    if (isset($address['billing_state']) && !empty($address['billing_state'])) {
                                                        echo $address['billing_state'] . ', ';
                                                    }
                                                    if (isset($address['billing_zipcode']) && !empty($address['billing_zipcode'])) {
                                                        echo $address['billing_zipcode'];
                                                        echo '<br>';
                                                    }
                                                    if (isset($address['billing_country']) && !empty($address['billing_country'])) {
                                                        echo $address['billing_country'];
                                                        echo '<br>';
                                                    }
                                                    ?>
                                                </p>
                                        @elseif($lead['type'] == 'phone_number')
                                            <?php
                                            $value = (isset($lead['telesales_data'][0]) && !empty($lead['telesales_data'][0])) ? $lead['telesales_data'][0] : [];
                                            ?>
                                            @if(!empty($value))
                                                <?php $value = (strlen($value['meta_value']) == 11) ? $value['meta_value'] : "1" . $value['meta_value']; ?>
                                                    <p><b>{{$lead['label']}}: </b>{{ preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config('constants.PHONE_NUMBER_REPLACEMENT'), $value) }}</p>
                                                </tr>
                                            @endif
                                        @else
                                            <?php
                                            $value = (isset($lead['telesales_data'][0]) && !empty($lead['telesales_data'][0])) ? $lead['telesales_data'][0] : [];
                                            ?>
                                            @if(!empty($value))
                                                <p><b>{{$lead['label']}}:</b> {{$value['meta_value']}}</p>
                                            @endif
                                        @endif
                                @endforeach
                                @if($telesale->status == 'verified')
                                <p><b>Verification Code:</b> {{ $telesale->verification_number}}</p>
                                @endif
                            </div>
                            </div>
                            @if(!empty($e_signature))
                            <div class="mt-15 agent-info bg-white border-line min-height-solve">
                                <h4><strong>E-signature</strong></h4>    
                                <img src="{{$e_signature}}" style="object-fit: cover; max-width:600px;"/>
                                </div>
                            @endif
                            <div class="mt-15 enrolment">
                                @php
                                  $count = 0;
                                @endphp
                                @foreach($programs as $program)
                                @php
                                  $count++;
                                  if ($loop->last && $count % 2 != 0) {
                                    $widthClass = "w-100";
                                  } else {
                                    $widthClass = "w-50";
                                  }
                                @endphp
                                <div class="{{$widthClass}} agent-info bg-white border-line min-height-solve">
                                    <h3>{{$program->utility->commodity}} Enrollment</h3>
                                    <div class="utility-outer">
                                        <h5 style ="font-weight:800;">{{$program->customer_type}}</h5>
                                        <p class="utility-sub-t"><strong>Program:</strong> {{$program->name}} </p>
                                        <div class="residential-table">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-3 br2 border-right">
                                                    <p>Code</p><span>{{$program->code}}</span>
                                                </div>
                                                <div class="col-md-3 col-sm-3">
                                                    <p>Rate</p> <span>${{ $program->rate }} per {{ $program->unit_of_measure }}</span>
                                                </div>
                                                <div class="col-md-2 col-sm-2">
                                                    <p>Term</p><span>{{$program->term}}</span>
                                                </div>
                                                <div class="col-md-2 col-sm-2">
                                                    <p>MSF</p><span>${{$program->msf}}</span>
                                                </div>
                                                <div class="col-md-2 col-sm-2">
                                                    <p>ETF</p><span>${{$program->etf}}</span>
                                                </div>
                                            </div>
                                            @php $customFields = getEnableCustomFields($program->client_id); @endphp
                                            @if(!empty($customFields))
                                            <br>
                                            <div class="row" style="border-top: 1px solid #20497C;padding-top: 10px;margin: 0px;">
                                                @foreach($customFields as $key => $fields)
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-3">
                                                            <p>{{$fields}} :</p>
                                                        </div>
                                                        <div class="col-md-9 col-sm-9">
                                                            <span>{{array_get($program,$key)}}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if($additionalDetails->childLeads()->count() > 0 ||    $additionalDetails->parentLead()->count() > 0))
                            <div class="mt-15 cust-info bg-white border-line min-height-solve">
                                    <div class="agent-info" style="padding: 0px !important;">
                                        <h3>Additional Enrollments</h3>
                                        @if($additionalDetails->childLeads->count() != 0)
                                        <div class="table-responsive">
                                            <table class="">
                                                <tbody>
                                                    @foreach($additionalDetails->childLeads as $key => $child)
                                                        <tr>
                                                            <td class="ld-title" style="width: 100%; color:#000; font-size: 13px; padding-right: 17px;">
                                                                Additional Enrollment {{ $key + 1 }}:
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('telesales.show', $child->id) }}">{{ $child->refrence_id }}</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>       
                                        @endif                         
                                        @if(isset($additionalDetails->parentLead) && !empty($additionalDetails->parentLead))
                                            <div class="table-responsive">
                                            <table class="">
                                                <tbody>
                                                   <tr>
                                                    <td class="ld-title" style="width: 100%; color:#000; font-size: 13px; padding-right: 17px;">
                                                           Additional Enrollment 1:
                                                       </td>
                                                       <td>
                                                        <a href="{{ route('telesales.show', $additionalDetails->parentLead->id) }}">{{ $additionalDetails->parentLead->refrence_id }}</a>
                                                       </td>
                                                   </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                        @endif  
                                    </div>
                                </div>
                            @endif
                        <div class="mt-15 cust-info bg-white border-line min-height-solve">
                            <div class=" agent-info" style="padding: 0px !important;">
                                <h2>Files</h2>                                
                                <table>
                                    <tbody>
                                        @if(!empty($telesale->contract_pdf) && ((($telesale->type == 'tele' && isOnSettings($telesale->client_id, 'is_enable_contract_tele')) || ($telesale->type == 'd2d' && isOnSettings($telesale->client_id, 'is_enable_contract_d2d'))) || ($telesale->client_id == config()->get('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID')))) 
                                        <tr>
                                            <td><p><strong>Contract:</strong></p></td>
                                            <td>
                                                <a data-toggle="tooltip"
                                                    data-placement="top"
                                                    data-original-title="Download Contract PDF"
                                                    class="btn"
                                                    target="_blank"
                                                    href="{{ Storage::disk('s3')->url($telesale->contract_pdf) }}"
                                                    download>
                                                    <img width="20"  src="{{asset('images/download.png')}}" />
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if(isset($telesale->s3_recording_url) && !empty($telesale->s3_recording_url))
                                        <tr>
                                            <td><p><strong>TPV Recording:</strong></p></td>
                                            <td>
                                                <a data-toggle="tooltip"
                                                    data-placement="top"
                                                    data-original-title="Download Tpv Recording"
                                                    class="btn"
                                                    target="_blank"
                                                    href="{{ Storage::disk('s3')->url($telesale->s3_recording_url) }}"
                                                    download>
                                                    <img width="20"  src="{{asset('images/play.png')}}" />
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if(isset($audio->url) && !empty($audio->url))
                                        <tr>
                                            <td><p><strong>Consent Recording:</strong></p></td>
                                            <td> 
                                                <a data-toggle="tooltip"
                                                    data-placement="top"
                                                    data-original-title="Download Consent Recording"
                                                    class="btn"
                                                    target="_blank"
                                                    href="{{ Storage::disk('s3')->url($audio->url) }}"
                                                    download>
                                                    <img width="20"  src="{{asset('images/play.png')}}" />
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if(isset($telesale->tpv_receipt_pdf) && !empty($telesale->tpv_receipt_pdf))
                                        <tr>
                                            <td><p><strong>TPV Receipt:</strong></p></td>
                                            <td>
                                                <a data-toggle="tooltip"
                                                    data-placement="top"
                                                    data-original-title="Download TPV RECEIPT"
                                                    class="btn"
                                                    target="_blank"
                                                    href="{{ Storage::disk('s3')->url($telesale->tpv_receipt_pdf) }}"
                                                    download>
                                                    <img width="20"  src="{{asset('images/download.png')}}" />
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>                               
                            </div>
                        </div>
                        <div class="mt-15 timeline bg-white min-height-solve border-line sales_tablebx">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2>Timeline</h2>
                                    <div class="pull-right">
                                        <div class="tab-slider--nav timeline-tab-view">
                                            <ul class="tab-slider--tabs">
                                                <li class="tab-slider--trigger active" rel="tab1">ON</li>
                                                <li class="tab-slider--trigger" rel="tab2">OFF</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-slider--container">
                                <div id="tab1" class="tab-slider--body">
                                    <section class="report-timeline">
                                        <ul class="cbp_tmtimeline">
                                            
                                            @foreach($criticalLogs as $logs)
                                                <li> <time class="cbp_tmtime"><span>{{Carbon\Carbon::parse($logs->formatted_created_at)->format(getDateFormat())}}</span> <span>{{Carbon\Carbon::parse($logs->formatted_created_at)->format(getTimeFormat())}}</span></time>
                                                    <?php
                                                        $lead_status = $logs->lead_status ;
                                                        if($logs->lead_status == 'Self Verified'){
                                                            $lead_status = 'Self-verified';
                                                        }
                                                    ?>
                                                    <div class="cbp_tmicon tm-{{ucfirst($lead_status)}}" data-toggle="tooltip" data-placement="bottom" data-container="body" title="{{ucfirst($logs->lead_status)}}"> 
                                                        <?php
                                                            switch ($logs->event_type) {
                                                                case in_array($logs->event_type,[1,2,3,4,5,6,7,8,9,10]):
                                                                    $icon =  getimage('/images/tm-alert.svg');
                                                                    break;
                                                                case in_array($logs->event_type,[12,16,29,30]):
                                                                    $icon =  getimage('/images/tm-commu.svg');
                                                                    break;
                                                                case in_array($logs->event_type,[17,18,19,20,21,22,23,24,25,26,27,28,40,41,42,43]):
                                                                    $icon =  getimage('/images/tm-phone-call.svg');
                                                                    break;
                                                                case in_array($logs->event_type,[11,13,14,15,31,32,33,34,35,36,37,38,39,43]):
                                                                $icon =  getimage('/images/tm-event.svg');
                                                                    break;
                                                            }
                                                        ?>
                                                        <i class="tm-icon"><?php echo  $icon; ?></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <h4 class="timeline-remark mt-0">{{$logs->reason}}</h4>
                                                        <div class="agent-info-div">
                                                        @if(isset($logs->user_type))
                                                            <h4 class="timeline-user"><i class="entypo-user" data-toggle="tooltip" data-placement="bottom" data-container="body" title="User"></i>{{$logs->user_type}}</h4>
                                                        @endif
                                                        @if(isset($logs->first_name))
                                                            <h4 class="timeline-user"><i class="fa fa-headphones" data-toggle="tooltip" data-placement="bottom" data-container="body" title="Sales Agent"></i>{{$logs->first_name}} {{$logs->last_name}}</h4>
                                                        @endif
                                                        @if(isset($logs->tpv_agent_first_name))
                                                            <h4 class="timeline-user"><i class="entypo-users"  data-toggle="tooltip" data-placement="bottom" data-container="body" title="TPV Agent"></i>{{$logs->tpv_agent_first_name}} {{$logs->tpv_agent_last_name}}</h4>
                                                        @endif
                                                        
                                                        <h4 class="timeline-user"><i class="tm-status tm-{{ucfirst($lead_status)}}" data-toggle="tooltip" data-placement="bottom" data-container="body" title="{{ucfirst($logs->lead_status)}}"></i>{{ucfirst($logs->lead_status)}}</h4>
                                                        
                                                            
                                                            <!---other types of status----->

                                                            <!-- <h4 class="timeline-user"><i class="tm-status tm-Verified"></i>Verified</h4>
                                                            <h4 class="timeline-user"><i class="tm-status tm-Declined"></i>Declined</h4>
                                                            <h4 class="timeline-user"><i class="tm-status tm-Disconnected"></i>Disconnected</h4>
                                                            <h4 class="timeline-user"><i class="tm-status tm-Cancelled"></i>Cancelled</h4>
                                                            <h4 class="timeline-user"><i class="tm-status tm-Expired"></i>Expired</h4> -->
                                                        </div>
                                                        <div class="tm-lead-status">
                                                        
                                                        @if($logs->related_lead_ids != '')
                                                            <h4>Related Leads</h4>
                                                            <p>
                                                                {!!$logs->related_lead_ids!!} 
                                                            </p>
                                                        @endif
                                                        </div>

                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </section>
                                </div>
                                <div id="tab2" class="tab-slider--body">
                                    <div class="table-responsive">
                                        <table id="critical-logs-timeline" class="table">
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $(function() {
        // initial sort set using sortList option
        <?php $count = 1; ?>
        <?php
        $alertIcon = '<img src="/images/alert-danger1.png" alt="Alert:" height="12px" width="12px" />';
        $pattern = array("/Alert:/", "/\r|\n/");
        $replace = array("$alertIcon", "</br>");
        ?>
        var dataSet = [
            @foreach($criticalLogs as $logs)[
                '{{$count++}}',
                '{{$logs->formatted_created_at}}',
                '{{$logs->user_type}}',
                '{{$logs->first_name . " " . $logs->last_name}}',
                '{{$logs->tpv_agent_val}}',
                '{{$logs->lead_status}}',
                '{!! preg_replace($pattern, $replace, $logs->reason ) !!}',
                '{!! $logs->related_lead_ids !!}'
            ],
            @endforeach
        ];
        $('#critical-logs-timeline').DataTable({
            data: dataSet,
            paging: false,
            info: false,
            searching: false,
            columns: [{
                    title: "Sr No.",
                    orderable: true
                },
                {
                    title: "Date & Time",
                    orderable: false
                },
                {
                    title: "User",
                    orderable: false
                },
                {
                    title: "Sales Agent",
                    orderable: false
                },
                {
                    title: "TPV Agent",
                    orderable: false
                },
                {
                    title: "Lead Status",
                    orderable: false
                },
                {
                    title: "Remark",
                    orderable: false,
                    width: "20%"
                },
                {
                    title: "Related leads",
                    orderable: false,
                    className: 'force-text-left'
                }
            ]
        });
    }); 

    /*--------time-line-tabs-toggle----*/

    $("document").ready(function() {
        $(".tab-slider--body").hide();
        $(".tab-slider--body:first").show();
    });

    $(".tab-slider--nav li").click(function() {
        $(".tab-slider--body").hide();
        var activeTab = $(this).attr("rel");
        $("#" + activeTab).fadeIn();
        if ($(this).attr("rel") == "tab2") {
            $('.tab-slider--tabs').addClass('slide');
        } else {
            $('.tab-slider--tabs').removeClass('slide');
        }
        $(".tab-slider--nav li").removeClass("active");
        $(this).addClass("active");
    });
</script>
@endsection
      