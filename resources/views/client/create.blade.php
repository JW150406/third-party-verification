@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(
  array('link' => route('client.index'), 'text' =>  'Clients'),
  array('link' => "", 'text' =>  "Create"),
);
breadcrum($breadcrum);
?>


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
		  @if ($message = Session::get('error'))
		  <div class="alert alert-error alert-dismissable">
			{{ $message }}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  @endif

		  <div class="tpvbtn message"></div>
		  <!--tab-new-design-start-->

		  <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
			<div class="client-bg-white">
			  <h1>Add Client</h1>
				<div class="tab-content">

					<!--about Details starts-->
					<div role="tabpanel" class="tab-pane active" id="About">
					  <div class="row">
						<form id="client-form" enctype="multipart/form-data" method="POST" action="{{ route('client.store') }}" onsubmit="return false;" data-parsley-validate>
						  {{ csrf_field() }}
						  @include('client.form')
						</form>
					  </div>
					</div>
					<!--about ends-->
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
