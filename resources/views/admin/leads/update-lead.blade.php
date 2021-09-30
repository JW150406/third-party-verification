@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(
    array('link' => 'javascript:void(0)', 'text' => 'Admin'),
    array('link' => "", 'text' => 'Update Lead')
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

    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="client-bg-white min-height-solve">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                                    <h1>Update Lead Status<span></span></h1>
                                </div>
                                <div class="message">
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12" style="margin-top:20px;">
                                    <div class="row">
                                        <form id="client-form" enctype="multipart/form-data" method="POST" action="{{ route('update.lead') }}" data-parsley-validate>
                                            {{ csrf_field() }}
                                            <div class="col-xs-12 col-sm-12 col-md-12" id="update-lead-status">
                                                <input type="hidden" value="1" name="update_lead_flag" id="update_lead_flag">
                                                <div class="col-xs-3 col-sm-3 col-md-3">
                                                    <div class="form-group">
                                                        <label class="yesstar">Reference id</label>
                                                        <input type="text" id="referenceId" class="form-control" value="" name="reference_id" data-id="id" data-parsley-required="true">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select onchange="getClientDispositions()" class="select2 form-control1-select1 edit-alert-select-class visible select2-hidden-accessible" name="status" id="lead_status" data-parsley-required="true" tabindex="-1" aria-hidden="true" data-select2-id="select2-status">
                                                            <option value=""  selected="" data-select2-id="select2-data-760-3a8g">Select</option>
                                                            <option value="verified">Verified</option>
                                                            <option value="decline">Decline</option>
                                                        </select>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="submit" name="submit" value="submit" class="btn btn-green mt20">                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="dispositions-outer row" style="margin-top: 20px;"></div>

                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                            
                            

                            <div class="row mt30">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<script>
function getClientDispositions() {
  console.log("Retrieve Dispositions");
  var referenceId = $("#referenceId").val();
  var disType = $("#lead_status").val();
  $.ajax({
      type: "POST",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('tpvagents.retrieve-dispositions-for-admin') }}",
      data: {
          'disType': disType,
          'referenceId': referenceId
      },
      success: function(res) {
        if (res.status == "success") {
            $(".dispositions-outer").html(res.data.view);
            if (res.data.totalReasons && res.data.totalReasons > 0) {
                //disableReadyNotReadyButtons();
            } else {
                //enableReadyNotReadyButtons();
            }
        } else {
          console.log(res.message);
        }
      }, error: function(err) {
        console.log(err);
      }
  });
}
</script>
@endsection
