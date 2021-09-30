@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
    $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');
    $breadcrum[] = array('link' => route("client.show", array($client->id)), 'text' =>  $client->name);
    $breadcrum[] = array('link' => route("client.show", array($client->id))."#SalesCenter", 'text' =>  "Sales Centers");
} else {
    // $breadcrum[] = array('link' => route('client.salescenters',$salescenter->client_id), 'text' =>  'Sales Centers' );
}
$breadcrum[] = array('link' => '', 'text' =>  $salescenter->name);
breadcrum($breadcrum);
?>
<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                    <!--tab-new-design-start-->
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissable">
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    <div class="tpvbtn message">

                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                        <div class="client-bg-white">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="new-info ">
                                        @empty($salescenter->logo)
                                        <img src="{{asset('images/PlaceholderLogo.png')}}">
                                        @else
                                        <img src="{{Storage::url($salescenter->logo)}}">
                                        @endempty
                                        <span>{{$salescenter->name}}</span>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="new-info pull-right">
                                        @empty($client->logo)
                                        <img src="{{asset('images/PlaceholderLogo.png')}}">
                                        @else
                                        <img src="{{Storage::url($client->logo)}}">
                                        @endempty
                                        <span>{{$client->name}}</span>
                                    </div>
                                </div>

                            </div>

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist" id="myTab">
                                <li role="presentation" class="active"><a href="#SalesAbout" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">About</a></li>
                                <li role="presentation"><a href="#SalesCenterLocation" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">Locations</a></li>
                                <li role="presentation"><a href="#SalesCenterUser" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">Sales Center Users</a></li>
                                <li role="presentation"><a href="#SalesAgent" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">Sales Agents</a></li>
                            </ul>
                            <!-- Tab panes -->

                            <div class="tab-content">

                                <!--about Details starts-->
                                <div role="tabpanel" class="tab-pane <?php if (\Request::has('page')) echo '';
                                                                        else echo 'active'; ?>" id="SalesAbout">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div class="agent-detailform v-star">
                                                <div class="alert-message-wrapper" style="width:80%; margin: 0 auto;">
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">


                                                    <div class="form-group">
                                                        <label for="name">Sales center name</label>
                                                        <input id="name" type="text" value="{{ $salescenter->name }}" placeholder="Sales center name" class="form-control required" name="name" disabled autofocus="">
                                                        <span class="form-icon"><img src="{{ asset('images/form-name.png')}}"></span>

                                                    </div>
                                                    <div class="form-group">
                                                        <label for="clientcode">Sales center Code</label>
                                                        <input id="clientcode" autocomplete="off" type="text" class="form-control required" name="code" value="{{ $salescenter->code }}" placeholder="Sales center code" disabled>
                                                        <span class="form-icon"><img src="{{ asset('images/code.png')}}"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="street">Address</label>
                                                        <input id="street" type="text" class="form-control" name="street" value="{{ $salescenter->street }}" placeholder="Address" disabled>
                                                        <span class="form-icon"><img src="{{ asset('images/location.png')}}"></span>

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label for="city">City</label>
                                                                <input id="city" type="text" class="form-control required" name="city" value="{{ $salescenter->city }}" disabled placeholder="City">
                                                            </div>
                                                        </div>


                                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label for="state">State</label>
                                                                <input id="state" type="text" class="form-control required" name="state" value="{{ $salescenter->state }}" disabled placeholder="State">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label for="country">Country</label>
                                                                <input id="country" type="text" class="form-control" name="country" value="{{ $salescenter->country }}" disabled placeholder="Country">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label for="zip">Zipcode</label>
                                                                <input id="zip" type="text" class="form-control" name="zip" value="{{ $salescenter->zip }}" disabled placeholder="Zipcode" maxlength="7">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <label for="contact">Contact</label>
                                                                <input id="contact" type="text" value="{{ $salescenter->contact }}" placeholder="Contact" class="form-control required" name="contact" disabled autofocus="">
                                                            </div>
                                                        </div>



                                                    </div>

                                                    <div class="col-xs-12 col-sm-12 col-md-12 bottom_btns">
                                                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                                            <div class="btn-group mt30 mb30">
                                                                <a href='{{route("client.salescenters.edit", array($client_id, $salecenter_id))}}' class="btn btn-green">Edit</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--about ends-->
                                </div>

                                <div role="tabpanel" class="tab-pane" id="SalesCenterLocation">
                                    @include('client.salescenter_new.location.index')
                                </div>
                                 <!--Sales center user starts-->
                                <div role="tabpanel" class="tab-pane" id="SalesCenterUser">
                                    @include('client.salescenter_new.user.index')
                                </div>

                                <!--Sales agent content starts-->
                                <div role="tabpanel" class="tab-pane" id="SalesAgent">
                                    @include('client.salescenter.salescenteragent')
                                </div>
                                <!--Sales agent content ends-->
                            </div>
                            @include('client.salescenter.salescenterpoup')
                        </div>
                    </div>
                    <!--tab-new-design-end-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
