@extends('layouts.admin')
@section('content')
<?php 
$breadcrum = array();
 
$breadcrum[] =  array('link' => route('utilities.index',['client' => $client_id]) , 'text' =>  'Utilities');
$breadcrum[] =  array('link' => '' , 'text' =>  "Import Utility"); 
breadcrum ($breadcrum);
 ?>

 <div class="tpv-contbx">
		<div class="container">
					<div class="col-xs-12 col-sm-8 col-md-8">
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
					   <div class="cont_bx3 salescenter_contbx">
							<h1>Import</h1>
                            <form   method="POST" action="{{ route('client.utility.parseimport',['client_id' => $client_id]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <p>CSV file to import</p>
                                            <div class=" {{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                            <input id="csv_file" type="file" class="file2 btn btn-purple" data-label="Browse <span class='browse'><img src='/images/browse_w.png'></span>" name="csv_file">

                                                @if ($errors->has('csv_file'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('csv_file') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 col-md-4">
                                            <button class="btn btn-green" type="submit">Submit<span class="add"><img src="/images/update_w.png"/></span></button>
                                        </div>
                                    </div> 
                             </form> 
                       </div>
                  </div>
         </div>
</div> 
@endsection
