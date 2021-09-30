@extends('layouts.admin')

@section('content')

<?php

$breadcrum = array();

$breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');

$breadcrum[] = array('link' => route('client.edit', ['id' => $client->id]), 'text' =>  $client->name);

$breadcrum[] = array('link' => url('admin/client') . '/' . $client->id . '/edit#EnrollmentForm', 'text' => 'Forms');

breadcrum($breadcrum);

?>




<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                        <div class="client-bg-white">

                            <h1>Edit Form</h1>

                            <div class="tpvbtn">
                                @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                                @endif
                            </div>

                            <form action="{{ route('client.salescenter.storeNew', $client->id)}}" enctype="multipart/form-data" role="form" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="">
                                            <div class="alert-message-wrapper" style="width:80%; margin: 0 auto;"></div>
                                            <div class="col-xs-12 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2">
                                                <div class="form-group mt30 {{ $errors->has('formname')   ? ' has-error' : '' }}">
                                                    <label for="name">Form name</label>
                                                    <input id="formname" type="text" placeholder="Form name" class="form-control required" name="formname" value="{{ old('formname') }}" required="" autofocus="">
                                                    <span class="form-icon"><img src="{{ asset('images/form-name.png')}}"></span>
                                                    @if ($errors->has('formname'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('formname') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                                <ul id="formfields" class="lead-ul">

                                                </ul>
                                            </div>
                                            <div class="col-xs-12 col-sm-2 col-md-2">

                                                <!-- <div class="form-group mt30 ">
                                                    <label for="new_field">Add new field</label>
                                                    <select id="select-box-admin" class="form-control">
                                                        <option value="">Select option</option>
                                                        @foreach($formFields as $key => $field)
                                                        <option value="{{$key}}">{{$field}}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="add-new-item">Add</button>
                                                </div> -->

                                                <div id="sticky-anchor"></div>
                                                <div id="sticky">
                                                    <div class="dropdown">
                                                        <label for="addnewfield">Add New Field</label>
                                                        <select class="select2 no-search select-box-admin" id="select-box-admin">
                                                            <option value="">Select option</option>
                                                            @foreach($formFields as $key => $field)
                                                            <option value="{{$key}}">{{$field}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="leadcreation">
                                                        <button type="button" class="btn btn-green add-new-item">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!--tab-new-design-end-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/js/admin-client-contact.js') }}"></script>
@endpush