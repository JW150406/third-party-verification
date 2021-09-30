@extends('layouts.app')
@section('content')

<style>
    .space-none {
        margin-top: 15px;
    }
    .cont_bx3 .pdlr0 {
        padding-left: 0px;
        padding-right: 0px;
    }
    .client-bg-white{
        min-height: auto;
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
                                    <h1>Reference ID: <span>{{$reference_id}}</span></h1>
                                </div>
                            </div>


                            <div class="row mt30">
                                <div class="col-xs-12 col-sm-12 col-md-12 leaddetail sales_tablebx lead-view">
                                    <div class="client-bg-white border-line min-height-solve">
                                        <h3>Personal Details</h3>
                                    <div class="table-responsive">
                                        <table class="table mb15">
                                            <tbody>
                                            @foreach($lead_detail as $lead)
                                            
                                                @if($lead['is_primary'] == 1)
                                                    
                                                    @if($lead['type'] == 'label')
                                                    @elseif($lead['type'] == 'phone_number')
                                                        <?php
                                                        $value = (isset($lead['telesales_data'][0]) && !empty($lead['telesales_data'][0])) ? $lead['telesales_data'][0] : [];
                                                        ?>
                                                        @if(!empty($value))
                                                            <?php $value = (strlen($value['meta_value']) == 11) ? $value['meta_value'] : "1" . $value['meta_value']; ?>
                                                            <tr>
                                                                <td>{{$lead['label']}}:</td>
                                                                <td>{{ preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config('constants.PHONE_NUMBER_REPLACEMENT'), $value) }}</td>
                                                            </tr>
                                                        @endif
                                                    @elseif($lead['type'] == 'email')
                                                    
                                                        <?php
                                                            $value = $lead['telesales_data'][0];
                                                        ?>
                                                        <tr>
                                                                <td>{{$lead['label']}}:</td>
                                                                <td>{{ $value['meta_value'] }}</td>
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
                                                        <td class="ld-title">{{$lead['label']}}:</td>
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
                                                        <td class="ld-title">Service Address:</td>
                                                        <td><?php
                                                            // if (isset($address['unit']) && !empty($address['unit'])) {
                                                            //     echo $address['unit'];
                                                            //     echo '<br>';
                                                            // }
                                                            // if (isset($address['address_1']) && !empty($address['address_1'])) {
                                                            //     echo $address['address_1'];
                                                            //     echo '<br>';
                                                            // }
                                                            // if (isset($address['address_2']) && !empty($address['address_2'])) {
                                                            //     echo $address['address_2'];
                                                            //     echo '<br>';
                                                            // }
                                                            if (isset($address['city']) && !empty($address['city'])) {
                                                                echo $address['city']. ', ';
                                                            }
                                                            if (isset($address['state']) && !empty($address['state'])) {
                                                                echo $address['state']. ", ";
                                                            }
                                                            if (isset($address['zipcode']) && !empty($address['zipcode'])) {
                                                                echo $address['zipcode'];
                                                                echo '<br>';
                                                            }
                                                            // if (isset($address['country']) && !empty($address['country'])) {
                                                            //     echo $address['country'];
                                                            //     echo '<br>';
                                                            // }
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
                                                        <td class="ld-title">Service Address:</td>
                                                        <td><?php
                                                        // if (isset($address['service_unit']) && !empty($address['service_unit'])) {
                                                        //     echo $address['service_unit'];
                                                        //     echo '<br>';
                                                        // }
                                                        // if (isset($address['service_address_1']) && !empty($address['service_address_1'])) {
                                                        //     echo $address['service_address_1'];
                                                        //     echo '<br>';
                                                        // }
                                                        // if (isset($address['service_address_2']) && !empty($address['service_address_2'])) {
                                                        //     echo $address['service_address_2'];
                                                        //     echo '<br>';
                                                        // }
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
                                                        // if (isset($address['service_country']) && !empty($address['service_country'])) {
                                                        //     echo $address['service_country'];
                                                        //     echo '<br>';
                                                        // }
                                                        ?>
                                                    </tr>
                                                <!--  <tr>
                                                        <td class="ld-title">Billing Address</td>
                                                        <td><?php
                                                            // if (isset($address['billing_unit']) && !empty($address['billing_unit'])) {
                                                            //     echo $address['billing_unit'];
                                                            //     echo '<br>';
                                                            // }
                                                            // if (isset($address['billing_address_1']) && !empty($address['billing_address_1'])) {
                                                            //     echo $address['billing_address_1'];
                                                            //     echo '<br>';
                                                            // }
                                                            // if (isset($address['billing_address_2']) && !empty($address['billing_address_2'])) {
                                                            //     echo $address['billing_address_2'];
                                                            //     echo '<br>';
                                                            // }
                                                            // if (isset($address['billing_city']) && !empty($address['billing_city'])) {
                                                            //     echo $address['billing_city'] . ', ';
                                                            // }
                                                            // if (isset($address['billing_state']) && !empty($address['billing_state'])) {
                                                            //     echo $address['billing_state'] . ', ';
                                                            // }
                                                            // if (isset($address['billing_zipcode']) && !empty($address['billing_zipcode'])) {
                                                            //     echo $address['billing_zipcode'];
                                                            //     echo '<br>';
                                                            // }
                                                            // if (isset($address['billing_country']) && !empty($address['billing_country'])) {
                                                            //     echo $address['billing_country'];
                                                            //     echo '<br>';
                                                            // }
                                                            ?>
                                                        </td>
                                                    </tr> -->

                                            @else(count($lead['telesales_data']) == 1)
                                                    <?php
                                                    $value = (isset($lead['telesales_data'][0]) && !empty($lead['telesales_data'][0])) ? $lead['telesales_data'][0] : [];
                                                    ?>
                                                    @if(!empty($value))
                                                        <tr>
                                                            <td>{{$lead['label']}}:</td>
                                                            <td>{{$value['meta_value']}}</td>
                                                        </tr>
                                                    @endif

                                                @endif
                                            @endif
                                            @endforeach
                                            
                                            @if(isset($dispositions) && !empty($dispositions) && count($dispositions) > 0 )
                                                <tr>
                                                    <td>Disposition:</td>
                                                    <td>
                                                        @foreach($dispositions as $alert)
                                                            {{$alert}} <br/>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($verificationCode != '')
                                            <tr>
                                                <td class="ld-title">Verification Code:</td>
                                                <td>{{ $verificationCode }}</td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div class="row">
                                <?php
                                $programCount = count($programs) > 0 ? count($programs): 1;
                                $classSize = 12 / $programCount;
                                $class = 'col-xs-' . $classSize . ' col-sm-' . $classSize . ' col-md-' . $classSize
                                ?>
                                @forelse($programs as $program)
                                    <div class="{{ $class }} leaddetail sales_tablebx lead-view mb15 mt20">
                                        <div class="client-bg-white border-line min-height-solve">
                                            <h3> {{ $program->utility->commodity }} Utility</h3>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>
                                                    <tr>
                                                        <td class="ld-title">Program ID:</td>
                                                        <td>{{ $program->code }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="ld-title">Program:</td>
                                                        <td>{{ $program->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="ld-title">Rate</td>
                                                        <td>${{ $program->rate }} per {{ $program->unit_of_measure }}</td>
                                                    </tr>
                                                    {{--<tr>
                                                        <td class="ld-title">Term (Months)</td>
                                                        <td>{{ $program->term }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="ld-title">ETF ($)</td>
                                                        <td>{{ $program->etf }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="ld-title">MSF ($)</td>
                                                        <td>{{ $program->msf }}</td>
                                                    </tr>--}}
                                                    <tr>
                                                        <td class="ld-title">Utility Name</td>
                                                        <td>{{ $program->utility->fullname }} ({{$program->utility->market}})</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                @empty

                                @endforelse
                            
                                    @if($additionalDetails->is_multiple == '1')
                                        @if($additionalDetails->childLeads->count() != 0)
                                        {{-- <div class="row mt30"> --}}
                                            <div class="col-xs-12 col-sm-12 col-md-12 leaddetail sales_tablebx lead-view">
                                                <div class="client-bg-white border-line min-height-solve">
                                                    <h3>Additional Enrollments</h3>
                                                    <div class="table-responsive">
                                                        <table class="table mb15">
                                                            <tbody>
                                                                @foreach($additionalDetails->childLeads as $key => $child)
                                                                    <tr>
                                                                        <td class="ld-title">Additional Enrollment {{ $key + 1 }}</td>
                                                                        <td>
                                                                            <a href="{{ route('profile.leaddetail', $child->refrence_id) }}">{{ $child->refrence_id }}</a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        {{-- </div> --}}
                                        @endif
                                    @endif
                                    @if($additionalDetails->is_multiple == '0')
                                        @if(isset($additionalDetails->parentLead) && !empty($additionalDetails->parentLead))
                                        {{-- <div class="row mt30"> --}}
                                            <div class="col-xs-12 col-sm-12 col-md-12 leaddetail sales_tablebx lead-view">
                                                <div class="client-bg-white border-line min-height-solve">
                                                    <h3>Additional Enrollments</h3>
                                                    <div class="table-responsive">
                                                        <table class="table mb15">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="ld-title">Additional Enrollment</td>
                                                                    <td>
                                                                        <a href="{{ route('profile.leaddetail', $additionalDetails->parentLead->refrence_id) }}">{{ $additionalDetails->parentLead->refrence_id }}</a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        {{-- </div> --}}
                                        @endif
                                    @endif
                                

                                <div class="col-xs-12 col-sm-12 sol-md-12 mylead-previousbtn mb15 mt15">
                                    <a href="{{url()->previous()}}" class="btn btn-green" type="submit">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
