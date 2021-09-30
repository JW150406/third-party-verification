@extends('layouts.admin')
@section('content')

{{breadcrum ($breadcrum)}}

<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="client-bg-white">
                            <h1>Role Detail</h1>

                            <div class="panel-body mt30">
                                <!-- Display Validation Errors -->
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

                                <form class="form-horizontal" role="form" method="POST" action="{{ url('admin/roles') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name" class="col-md-4 control-label">Name</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus autocomplete="off">

                                            @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('display_name') ? ' has-error' : '' }}">
                                        <label for="display_name" class="col-md-4 control-label">Display Name:</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text" class="form-control" name="display_name" value="{{ old('display_name') }}" required autocomplete="off">

                                            @if ($errors->has('display_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('display_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                        <label for="email" class="col-md-4 control-label">Description</label>

                                        <div class="col-md-6">
                                            <textarea rows="4" cols="50" name="description" id="description" class="form-control">{{ old('description') }}</textarea>

                                            @if ($errors->has('description'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('permissions') ? ' has-error' : '' }}">
                                        <label for="permissions" class="col-md-4 control-label">Permissions</label>

                                        <div class="col-md-6">
                                            @foreach ($permissions as $key => $permission)
                                            <input type="checkbox" value="{{$key}}" name="permissions[]"> {{$permission}}<br>
                                            @endforeach

                                            @if ($errors->has('permissions'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('permissions') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>



                                    <div class="form-group mt30">
                                        <div class="col-md-8 col-md-offset-4">
                                            <button type="submit" class="btn btn-green">Save</button>
                                            <a type="button" class="btn btn-red" href="{{ url('admin/roles') }}">Cancel</a>


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








    @endsection