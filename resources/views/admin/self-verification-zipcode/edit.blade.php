@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "", 'text' => 'Self Verification Allowed Zipcodes')
        //array('link' => "", 'text' => 'Workspace')
    );
    $star = "yesstar";
    breadcrum($breadcrum);
    ?>
    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="client-bg-white">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="mt10">Self Verification Allowed Zipcodes</h1>
                                    </div>
                                    
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
                                <div class="sales_tablebx mt30">
                                    <form action="{{route('selfVerificationAllowedZipcode.store')}}" method="post">
                                    @csrf
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group {{ $errors->has('zipcode') ? ' has-error' : '' }}">
                                            <label for="all-zip">Zipcodes</label>
                                            <div class="zipcode-all scrollbar-inner" id="all-zip">
                                            @foreach($zipcodes as $zipcode)
                                                @if(!empty($zipcode->zipCode))
                                                <div class="alert alert-defualt alert-dismissible">
                                                    <input type="hidden" name="zipcode[]"   value="{{$zipcode->zipCode->zipcode}}" />
                                                    <a href="javascript:void(0)" class="close close-zipcode" data-dismiss="alert" aria-label="close">×</a>
                                                    {{$zipcode->zipCode->zipcode}}
                                                </div>
                                                @endif
                                            @endforeach
                                            </div>
                                            @if ($errors->has('zipcode'))
                                            <span class="help-block">
                                                {{ $errors->first('zipcode') }}
                                            </span>
                                            @endif
                                        </div>
                                        <span id="zip-error" class="error"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group autocomplete">
                                            <input id="auto_suggest_zip" type="text" class="form-control" placeholder="Find & Add">
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <div class="btn-group mt30 mb30">
                                            <button  type="submit" class="btn btn-green">Save
                                                </button>
                                           <!--  <a href="{{route('client.index') }}" id="client-cancel-btn" class="btn  btn-red">Cancel </a> -->

                                        </div>
                                    </div>
                                    </form>

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
@include('client.utility_new.auto-suggest-zipcode')
@endpush