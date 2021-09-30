@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' => '', 'text' =>  'Import Zipcode');

breadcrum($breadcrum);
?>

<div class="tpv-contbx">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12">
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
                <form method="POST" action="{{ route('client.utility.parsezipimport') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="row">
                        @if(Auth::user()->access_level =='tpv')
                        <div class="col-xs-12 col-sm-4 col-md-4">
                            <p>Select Client</p>
                            <div class="dropdown {{ $errors->has('name') ? ' has-error' : '' }}">
                                <select class="selectmenu select-box-admin" required name="client" id="client">
                                    <option value="">Select</option>
                                    @if(count($clients)>0)
                                    @foreach($clients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->access_level =='client')
                        <input type="hidden" name="client" id="client" value="{{ $client_id }}">
                        @endif
                        <div class="col-xs-12 col-sm-6 col-md-6">
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
                            <button class="btn btn-green" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection