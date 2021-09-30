@extends('layouts.tpvagent')

@section('content')
<?php
$disposition_id = "";
?>
<div class="container-wrapper">

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">


        <div class="panel-body marginbtm270">
          <!-- Display Validation Errors -->
          <div class="saleupdatenotification"></div>
          @if (count($errors) > 0)
          <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          @if ($message = Session::get('success'))
          <div class="alert alert-success">
            <p>{{ $message }}</p>
          </div>
          @endif
          <form class="form-horizontal" id="verify_agent_lead" enctype="multipart/form-data" role="form" method="GET" action="">
            <div class="new_telesale_reference text-success"></div>

            {{ csrf_field() }}
            <input type="hidden" value="" name="agent_client_id" id="agent_client_id">
            <input type="hidden" value="" name="agent_user_id" id="agent_user_id">
            <input type="hidden" value="" name="telesale_id" id="telesale_reference_id">
            <input type="hidden" value="" name="telesale_form_id" id="telesale_form_id">
            <input type="hidden" value="" name="form_worksid" id="form_worksid">
            <input type="hidden" value="" name="form_workflid" id="form_workflid">
            <input type="hidden" value="" name="current_lang" id="current_lang">
            <input type="hidden" value="" name="form_id" id="current_form_id">
            <input type="hidden" value="" name="leadzipcodestate" id="leadzipcodestate">
            <input type="hidden" value="" name="leadcommodity" id="leadcommodity">
            <input type="hidden" value="" name="cloned" id="allow_cloning">

            <h2 class="text-center waiting-for-call"> Waiting for call</h2>
            <div class="sale-detail-wrapper col-sm-8">

            </div>
            <div class="col-sm-4 agent-detail-wrapper scrollbar-inner"></div>

            <div class="script-important-buttons ">
              <!-- <button type="button" style="display:none" class="agent_not_found btn btn-danger" >Agent Not Found</button> -->
              <button type="button" style="display:none" class="telesale_not_found btn btn-danger">Telesale Not Found Create New</button>
              <button type="button" style="display:none" class="create-telesale btn btn-success">Submit new lead</button>
              <!-- <button type="button" style="display:none" class="btn btn-danger decline-form">Decline</button> -->
              <button type="button" class="script_for_confirmation btn btn-success salesagentintro" style="display:none;">Next</button>

              <a style="display:none" data-target="#confirmreview" data-toggle="modal" href="javascript:void(0);" class="btn btn-success verify-sale">Verify</a>&nbsp;&nbsp;
            </div>
          </form>


          <form class="form-horizontal decline-sale-form" id="decline-sale-form" enctype="multipart/form-data" role="form" method="GET" action="" onSubmit="return false;" style="display:none;margin-top:30px">
            <br />
            <br />
            <div class="reason-decline-msg">

            </div>
            {{ csrf_field() }}
            <div class="form-group">
              <label for="name" class="control-label inline-block blk-reason" style="padding-top: 2px;    float: left;    padding-left: 15px;"> Reason</label>

              <div class="col-md-6">
                <ul class="list-inline blk-reason">

                  @if(count($dispositions) > 0)

                  @foreach($dispositions as $singledisposition)
                  <li class="decline_dispositions">
                    <span class="radio-inline"><input type="radio" name="reason" class="getreason_for_decline @if($singledisposition->allow_cloning == 'true') clone_lead @endif" value="{{$singledisposition->id}}"></span>
                    <span> {{$singledisposition->description}}</span>
                  </li>
                  @endforeach
                  @endif
                  @if(count($hangup_dispositions) > 0)
                  <?php $j = 0 ?>
                  @foreach($hangup_dispositions as $single_hangup_disposition)
                  <li class="hangup_dispositions ">
                    <span class="radio-inline"><input type="radio" name="reason" class="getreason_for_decline" value="{{$single_hangup_disposition->id}}"></span>
                    <span> {{$single_hangup_disposition->description}}</span>
                  </li>
                  <?php $j++ ?>
                  @endforeach
                  @endif

                  <li>
                    <span class="radio-inline"><input type="radio" name="reason" class="getreason_for_decline" value="other"></span>
                    <span>Other</span>
                  </li>
                </ul>
                <input type="hidden" id="decline_reason" name="decline_reason" value="Incomplete information" class="form-control decline_reason">

              </div>
              <div class="clearfix"></div>
              <div class="form-group col-md-8">

                <button type="submit" class="btn btn-danger decline-confirm" style="margin-top:10px; margin-left:15px;">
                  Decline
                </button>
                <button type="button" class="btn btn-danger save-and-clone" style="margin-top:10px; margin-left:15px;display:none">
                  Save and Clone
                </button>

              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

@include('tpvagents.scriptpopup.scriptbox')
@include('layouts.chatbox')

<!-- Modal -->
<div class="modal fade" id="confirmreview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('tpvagents.sales.update')}}" method="get" id="updatetelesalestatus">
        <input type="hidden" name="ref" value="{{$reference_id}}" id="reference_id_to_update">
        <input type="hidden" name="v" value="2" id="verification_code">
        <input type="hidden" value="" name="userid" id="userid">
        <input type="hidden" value="" name="form_id" id="script_form_id">
        <input type="hidden" value="0" name="is_multiple" id="is_multiple">
        <input type="hidden" value="0" name="multiple_parent_id" id="multiple_parent_id">
        <input type="hidden" value="" name="form_worksid" class="form_worksid">
        <input type="hidden" value="" name="form_workflid" class="form_workflid">
        <input type="hidden" value="" name="current_lang" id="script_current_lang">
        <input type="hidden" value="" name="leadzipcodestate" class="leadzipcodestate">
        <input type="hidden" value="" name="leadcommodity" class="leadcommodity">
        <input type="hidden" value="{{$disposition_id}}" name="disposition_id" id="disposition_id">
        {{ csrf_field() }}
        {{ method_field('GET') }}

        <input type="hidden" name="decline_reason" value="Incomplete information" class="form-control decline_reason">
        <!-- <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Alert!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div> -->
        <div class="modal-body text-center">
          <div class="mt15 text-center mb30"><?php echo getimage('/images/alert-danger.png') ?></div>
          <div class="mt15">
          Are you sure you want to <strong class="status-change-to"></strong> this sale?
          </div>
        </div>
        <div class="modal-footer pd0">
          <div class="btnintable bottom_btns pd0">
            <div class="text-center sale-red-btn">
              <button type="submit" class="btn btn-green">Confirm</button>
              <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="editoptionsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="" onsubmit="javascript:void(0);" method="get" id="editoptionsmodalform">
        <div class="modal-header">
          <h5 class="modal-title" id="editoptions_label_header"> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body edit-options">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success select_values_of_new_option">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  window.telesaleverifyagent = "{{route('telesaleverifyagent')}}";
  window.telesaleverifysaleid = "{{route('telesaleverifysaleid')}}";
  window.telesaleverifylead = "{{route('telesaleverifylead')}}";
  window.replicate = "{{route('telesale_clone_lead')}}";
  window.updatelead = "{{route('telesale_update_lead')}}";


  $('body').on('click', '.verify-sale', function() {
    $('#verification_code').val('1');
    $('.status-change-to').html('Verify');
    $('.decline-sale-form').hide();

  });
  $('body').on('click', '.decline-confirm', function() {
    if (disconnected_by_agent === true) {
      $('#verification_code').val('2');

    }
    $('.status-change-to').html('Decline');

  });

  $('body').on('click', '.decline-form', function() {
    $('.decline-sale-form').toggle();
  });
</script>
@endsection
