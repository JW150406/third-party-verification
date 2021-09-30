@extends('layouts.selfverify')
@section('content')
<div class="">
    <div class="container signature-outer">
        <div class="row">
            <div class="col-md-6 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h3>Signature </h3>
                    </div>
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissable" data-auto-dismiss="5000">
                            {{ $message }}
                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissable" data-auto-dismiss="5000">
                            {{ $message }}
                        </div>
                        @endif
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissable" data-auto-dismiss="2000">
                          {{$errors->first()}}
                        </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection