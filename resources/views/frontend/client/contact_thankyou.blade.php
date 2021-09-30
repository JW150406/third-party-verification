@extends('layouts.app')

@section('content')
<?php
$request = \Request::all();
?>

<style>
  .thankmessage strong {
    font-size: 28px;
  }
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

                <!--agent details starts-->

                <div class="col-xs-12 col-sm-12 col-md-12 pdlr0">        
                <!-- <h1 class="thankyou-client-heading">{{$company->name}}</h1> -->

                <div class="lead-sucess-img">
                    <img src="/images/lead-success.png">
                </div>
                <div class="thank-you-text">
                    <h3 class="thank-you-bold">Success!</h3>
                    <p class="thankmessage"> Your lead has been submitted.</p>
                    <p> Reference ID : <strong> {{ $request['ref'] }} </strong></p>
                    @if(isOnSettings($client_id,'is_enable_cust_call_num'))
                        <p> Verification Number : <strong>{{$contact_number ?? '' }} </strong></p>
                    @endif

                </div>
                @if(isOnSettings($client_id, 'is_enable_self_tpv_tele') && $restrict_state == 1)
                <div class="mt20 mb15 text-center">
                    <form method="post" action="{{route('store.selfverify',$lead->id)}}">
                        @csrf
                        <h4>Choose method of self verification :</h4>
                        <div class="form-group choose-verif-thanks">
                            <input class="styled-checkbox verify-mode" id="verify_email" type="checkbox" name="verification_mode[]" value="email">
                            <label for="verify_email" style="margin-right: 15px">Email</label>
                            <input class="styled-checkbox verify-mode" id="verify_text" type="checkbox" name="verification_mode[]" value="phone">
                            <label for="verify_text">SMS</label>
                        </div>
                        @if ($errors->has('verification_mode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('verification_mode') }}</strong>
                            </span>
                        @endif
                        <div class="form-group">
                            <button type="submit" id="verify-sub-btn" class="btn btn-green mr15" disabled>Self Verify </button>
                        </div>
                    </form>
                </div>
                @endif

              <div class="mt20 mb15 text-center dash-lead-btn">
                <a href="{{ route('my-account') }}" class="btn btn-green mr15">Dashboard</a>
                <a href="{{ route('profile.leads') }}" class="btn btn-green">My Leads</a>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12">
                @if( isset($request['ref'] ) && isset($request['call']) && $request['call'] == 'schedule_a_call' )
                <form action="{{route('ajax-schedulecall')}}" class="schedule_call_form" id="schedule_call_form" method="post">
                  <input type="hidden" value="{{$request['ref']}}" name="ref" />
                  <h2 class="thankyou-client-heading">Schedule a call</h2>
                  <div class="ajax-response"></div>
                  <div class="form-group text-center radio-btns flex form-field-wrapper col-xs-12 text-left" rel="radio" style="text-align: left;">
                    <label></label>
                    <span class="">
                      <label for="schedule_a_call" class="radio-inline">
                        <input type="radio" name="call_immediately" value="no" id="schedule_a_call" checked> <span>Schedule a call</span></label>

                    </span>
                    <span class="">
                      <label for="call_immediately" class="radio-inline">
                        <input type="radio" name="call_immediately" value="yes" id="call_immediately"> <span>Call Immediately</span></label>

                    </span>
                    <span class="invalid-feedback validation-error">
                      <strong></strong>
                    </span>
                  </div>

                  <div class="col-xs-12 col-sm-4 col-md-4 schedule_call">
                    <div class="form-group">
                      <label for="exampleInputName2">Date</label>
                      <input class="form-control datepicker" name="schedule_date" id="exampleInputName2" placeholder="Date" type="text" value="<?php echo date("m/d/Y") ?>">
                      <span class="form-icon"><img src="/images/calender.png"></span>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-2 col-md-2 schedule_call">
                    <div class="form-group">
                      <label for="time">Time</label>
                      <input type="text" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-default-time="<?php echo date("H:i A") ?>" data-show-meridian="true" data-minute-step="5" name="schedule_time" />
                      <span class="form-icon"><img src="/images/calender.png"></span>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-xs-6 col-sm-2 col-md-2">
                    <div class="form-group">
                      <label for="" style="visibility:hidden">Time</label>
                      <button class="btn btn-green" type="submit"><span class="save-text"> Save</span> <span class="add"><img src="/images/update_w.png"></span> </button>
                    </div>
                  </div>


                </form>

                @endif
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $(".verify-mode").click(function(){
            if($('.verify-mode:checked').length > 0) {
                $("#verify-sub-btn").prop('disabled',false);
            } else {
                $("#verify-sub-btn").prop('disabled',true);
            }
        });
    });
</script>
@endpush