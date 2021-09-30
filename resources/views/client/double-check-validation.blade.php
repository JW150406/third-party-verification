@extends('layouts.app')
@section('content')



<style type="text/css">
  .bottom_btns .btn.btn-green {
	margin: 0 10px;
  }
</style>


<div class="tpv-contbx">
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

		  <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
  			<div class="client-bg-white">
          <div class="mt15 mb15 text-center">
            	<span class="lead-alert-img"><?php echo getimage('/images/alert-danger.png') ?></span>
            	<h3 class="text-center">This enrollment triggered the following {{count($validationData) > 1 ? 'alerts' : 'alert' }}:</h3>
            </div>
            @foreach($validationData AS $data)
              <div class="row mb15">
              	<div class="col-md-1 col-md-offset-1 col-sm-1 col-sm-offset-1">
              	    <div class="right-alt-img"></div>
              	</div>
              	<div class="col-md-8  col-sm-8">
              	    <div class="trig-alert-outer">
                  		<h4>{{$data['title']}}</h4>
                  		<p>{{$data['msg']}}</p>
              	    </div>
              	</div>
              	<!--end-col-8-->
              </div>
            @endforeach
            <div class="row mb15 mt15">
              <div class="bottom_btns text-center">
              <a href="{{route('client.proceed_lead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn, 'is_multiple'=> $is_multiple ?? 0])}}"
                      class="btn btn-green submitBtn">Proceed
              </a>
              <a href="{{route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn, 'is_multiple'=> $is_multiple ?? 0])}}"
                      class="btn btn-red submitBtn">Cancel Lead
              </a>
            </div>
            </div>

  			</div>
		  </div>
		 <!--tab-new-design-end-->
		</div>
	  </div>
	</div>
  </div>
</div>
@endsection
