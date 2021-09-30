<!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ array_get($telesale, 'refrence_id') }}}</title>
    <style>
        @page {
            margin: 100px 25px 50px;
        }
        header {    
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: left;
            line-height: 35px;
        }
        body {
            font-family: "Helvetica";
            padding: 15px;
            font-size: 12px;
            border: 1px solid #000;
        }
        .lead-update {
            vertical-align: top;
            margin-left: 15px;
            padding: 5px 10px;
            border: 1px solid #000;
        }
        .lead-update p {
            font-size: 13px;
            color: #000;
            line-height: normal;
            margin: 0;
            font-weight: 600;
        }
        .verify-label {
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 600;
            padding: 5px 20px;
            background-color: #1f4d82;
            color: #fff;
            display: inline-block;
        }
        .agent-info {
            border: 1px solid #000;
            padding: 10px;
        }
        .w-50 {
            width: 50%;
        }
        .mt-15 {
            margin-top: 15px;
        }
        .w-24 {
            width: 24%;
        }
        .utility-outer {
            box-shadow: 0px 0px 14px #00000012;
            padding: 10px;
        }
        .utility-outer h5 {
            color: #000;
            font-size: 14px;
            font-weight: 700;
        }
        .utility-outer p {
            font-weight: 600;
        }
        .row {
            margin-left: -15px;
            margin-right: -15px;
        }
        .utility-outer .br2 {
            border-right: 2px solid #1c5997;
        }
        .lead-select {
            display: block;
            padding: 0;
            position: relative;
        }
        .residential-table {
            width: 100%;
            padding: 10px;
            border: 2px solid #204b7e;
        }
        .residential-table tr th,
        .residential-table tr td{
            padding:5px;
            text-align: left;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<h3>Lead ID: {{ array_get($telesale, 'refrence_id') }} </h3>

<table class="mt-15">
    <tr>
        <td>
            <div class="verify-label">

                <?php
                    echo config("constants.VERIFICATION_STATUS_CHART.".ucfirst($telesale->status));
                ?>
            </div>
        </td>
        <td>
            <div class="lead-update">
                <p>Lead submission on: <span>{{\Carbon\Carbon::parse(array_get($telesale, 'created_at'))->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</span></p>
            </div>
        </td>
        <td>
            <div class="lead-update">
                <p>Last updated on: <span>{{\Carbon\Carbon::parse(array_get($telesale, 'updated_at'))->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</span></p>
            </div>
        </td>
    </tr>
</table>
<table style="width: 100%;" class="mt-15">
    <tr>
        <td class="w-50 agent-info">
            <p><b>Agent:</b> {{ array_get($telesale, 'userWithTrashed') ? array_get($telesale->userWithTrashed, 'first_name') . " " . array_get($telesale->userWithTrashed, 'last_name') : "" }}</p>
            <p><b>ID:</b> {{ array_get($telesale, 'userWithTrashed') ? array_get($telesale->userWithTrashed, 'userid') : "" }}</p>
            <p><b>Email:</b> {{ array_get($telesale, 'userWithTrashed') ? array_get($telesale->userWithTrashed, 'email') : "" }}</p>
            @if((array_get($telesale, 'userWithTrashed') && array_get($telesale->userWithTrashed, 'salesAgentDetails')) && array_get($telesale->userWithTrashed->salesAgentDetailsWithTrashed, 'phone_number'))
                <p><b>Phone:</b> {{ (array_get($telesale, 'userWithTrashed') && array_get($telesale->userWithTrashed, 'salesAgentDetailsWithTrashed')) ? array_get($telesale->userWithTrashed->salesAgentDetailsWithTrashed, 'phone_number') : "" }}</p>
            @endif
        </td>
        <td class="w-50 agent-info">
            <p><b>Client:</b> {{$salesAgent->client_name}}</p>
            <p><b>Sales Center:</b> <?php echo ucfirst($salesAgent->name);?></p>
            <p><b>Sales Center Location:</b> <?php if(!empty($telesale->userWithTrashed) && !empty($telesale->userWithTrashed->salesAgentDetailsWithTrashed) && !empty($telesale->userWithTrashed->salesAgentDetailsWithTrashed->location)) {
                    echo ucfirst($telesale->userWithTrashed->salesAgentDetailsWithTrashed->location->name);
                } ?></p>
            <p><b>Channel:</b> <?php echo strtoupper($salesAgent->agent_type);?></p>

        </td>
    </tr>
</table>

<table style="border: 1px solid black; width: 100%" class="mt-15">
    <tbody>
    @foreach($leadDetail as $key => $lead)
        @if($lead['type'] == 'label')

            <tr>
                <td colspan="2"><h3>{{$lead['label']}}</h3></td>
            </tr>

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
            <tr>
                <td width="100"><b>{{$lead['label']}}</b>:</td>
                <td>{{ implode(' ', $fullName) }}</td>
            </tr>
        @elseif($lead['type'] == 'address')
            <?php
            $address = [];
            foreach ($lead['telesales_data'] as $value) {
                $address[$value['meta_key']] = $value['meta_value'];
            }
            ?>
            <tr>
                <td width="100"><b>{{$lead['label']}}:</b></td>
                <td><?php
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
                        echo $address['city'];
                    }
                    if (isset($address['state']) && !empty($address['state'])) {
                        echo $address['state'];
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
                </td>
            </tr>
        @elseif($lead['type'] == 'service_and_billing_address')
            <?php
            $address = [];
            foreach ($lead['telesales_data'] as $value) {
                $address[$value['meta_key']] = $value['meta_value'];
            }
            ?>
            <tr>
                <td width="100"><b>Service Address:</b></td>
                <td><?php
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
            </tr>
            <tr>
                <td width="100"><b>Billing Address:</b></td>
                <td><?php
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
                </td>
            </tr>
        @else
            <?php
                $value = (isset($lead['telesales_data'][0]) && !empty($lead['telesales_data'][0])) ? $lead['telesales_data'][0] : [];
            ?>
            @if(!empty($value))
            <tr>
                <td width="100"><b>{{$lead['label']}}:</b></td>
                <td>{{$value['meta_value']}}</td>
            </tr>
            @endif
        @endif
    @endforeach
    </tbody>
</table>

<table style="width: 100%;" class="mt-15" >
    <tr>
        @foreach($programs as $program)
            <td class="agent-info">
                <div style="text-align: center;">
                    <h3>{{$program->utility->commodity}} Enrollment</h3>
                </div>
                <p style="font-size: 12px">{{$program->customer_type}}</p>
                <p style="font-size: 10px; color: #949494;">Program: {{$program->name}} </p>
                <table class="residential-table">
                    <thead>
                        <tr>
                            <th style="border-right: 2px solid #204b7e;">Code</th>
                            <th>Rate</th>
                            <th>Term</th>
                            <th>MSF</th>
                            <th>ETF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border-right: 2px solid #204b7e;">{{$program->code}}</td>
                            <td>${{ $program->rate }} per {{ $program->unit_of_measure }}</td>
                            <td>{{$program->term}}</td>
                            <td>${{$program->msf}}</td>
                            <td>${{$program->etf}}</td>
                        </tr>
                        @foreach(getEnableCustomFields($program->client_id) as $key => $fields)
                            <tr>
                                <th @if($loop->first) style="border-top: 2px solid #204b7e;" @endif>{{$fields}}</th>
                                <td @if($loop->first) style="border-top: 2px solid #204b7e;" @endif  colspan="4">{{array_get($program,$key)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>


        @endforeach
    </tr>
</table>
<div class="page-break"></div>
<h3 class="mt-15"><u>Timeline</u></h3>
<table cellspacing="0" style="width: 100%;  table-layout: fixed; text-align:center; font-size: 10px;border: 1px solid black;" >
    <tr>
        <th width="5%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">Sr.no.</th>
        <th width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">Date & Time</th>
        <th width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">User</th>
        <th width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">Sales Agent</th>
        <th width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">TPV Agent</th>
        <th width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">Lead Status</th>
        <th width="34%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">Remark</th>
        <th width="10%" style="word-wrap: break-word;overflow-wrap: break-word; border-bottom: 1px solid black; ">Related Leads</th>
    </tr>
@if($criticalLogs->count() >0)

        <?php $count = 1; ?>
        @foreach($criticalLogs as $logs)
            <tr>
                <td width="5%" style="border-bottom: 1px solid black;border-right: 1px solid black;">{{$count++}}</td>
                <td width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">{{$logs->created_at->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</td>
                <td width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">{{$logs->user_type}}</td>
                <td width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">{{$logs->first_name . " " . $logs->last_name}}</td>
                <td width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">{{$logs->tpv_agent_first_name." ".$logs->tpv_agent_last_name}}</td>
                <td width="10%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">{{$logs->lead_status}}</td>
                <td width="34%" style="border-bottom: 1px solid black;border-right: 1px solid black; ">{{$logs->reason}}</td>
                <td width="10%" style="word-wrap: break-word;overflow-wrap: break-word; border-bottom: 1px solid black;">{{ str_replace(",", ", ", $logs->related_lead_ids) }}</td>

            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" align="center" style="background:#fff">
            No Record Found
            </td>
        </tr>
    @endif
</table>
</body>
</html>
