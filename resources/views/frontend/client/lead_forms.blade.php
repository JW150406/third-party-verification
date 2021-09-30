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
</style>


<div class="tpv-contbx edit-agentinfo">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                  		  @if ($message = Session::get('success'))
                  		  <div class="alert alert-success alert-dismissable">
                  			{{ $message }}
                  			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  			  <span aria-hidden="true">&times;</span>
                  			</button>
                  		  </div>
                  		  @endif
                  		  @if ($message = Session::get('error'))
                  		  <div class="alert alert-error alert-dismissable">
                  			{{ $message }}
                  			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  			  <span aria-hidden="true">&times;</span>
                  			</button>
                  		  </div>
                  		  @endif
                    <div class="col-xs-12 col-sm-12 col-md-12 pdlr0">
                        <div class="client-bg-white">
                            <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                                <h1>New Enrollment</h1>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!--agent details starts-->
                                <div class="row">
                                    <div class="col-xs-9 col-sm-12 col-md-6 pr-0">
                                        <div class="agent-detailform" style="padding-left: 15px;">
                                            <!-- <div class="center-content"> -->
                                            
                                            <select class="select2 form-control formSelect validate required"
                                                title="Please select form">
                                                <option value="">Select</option>
                                                @forelse($forms as $form)
                                                <option value="{{$form->id}}">{{ $form->formname }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                           
                                            <span id="select2-formenrollment-error-message" style="color:red;"></span>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                    <div class="col-xs-3 col-sm-12 col-md-2 pl-0">
                                    <div class="agent-detailform">
                                    <span class="input-group-btn">
                                                <a class="btn btn-default searchzipcode submitForm" style="padding:7px; border-radius: 2px;">Next</a>
                                            </span>
                                            </div>
                                    </div>
                                </div>
                                <!--agent details ends-->
                               {{-- <a class="btn btn-green text-center commodity_selection mb15" href="{{ route('client.contact.from', [$client->id, $form->id]) }}">{{ $form->formname }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function(){
        $(document).on('change','.formSelect',function(){
            console.log($(this).val());
            if($(this).val() != '')
            {
                let route = "{{route('client.contact.from',['client','form'])}}";
                route = route.replace('client','{{$client->id}}');
                route = route.replace('form',$(".select2 option:selected").val());
                $('.submitForm').attr('href',route);
                $('.submitForm').removeClass('cursor-none');
                $('.submitForm').attr('disabled',false);
                $('#select2-formenrollment-error-message').html('');
            }
            else
            {
                $('#select2-formenrollment-error-message').html('Please select form');
                $('.submitForm').addClass('cursor-none');
                $('.submitForm').attr('disabled',true);
                $('.submitForm').attr('href','javascript:void(0)');
            }
        })
    })
</script>
@endpush
