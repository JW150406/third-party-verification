@extends('layouts.admin')
@section('content')

<?php
$breadcrum[] = array('link' => "", 'text' =>  $role->display_name);
?>
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


                                <form class="form-horizontal" role="form" method="POST" action="{{ url('admin/roles/'.$role->id) }}">
                                    {{ csrf_field() }}
                                    {{ method_field('PATCH') }}

                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="display_name" class="col-md-4 control-label">Display Name</label>

                                        <div class="col-md-6">
                                            <input id="display_name" type="text" class="form-control" name="display_name" value="{{$role->display_name}}" required autofocus>

                                            @if ($errors->has('display_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('display_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                        <label for="description" class="col-md-4 control-label">Description</label>

                                        <div class="col-md-6">
                                            <textarea rows="4" cols="50" name="description" id="description" class="form-control">{{$role->description}}</textarea>

                                            @if ($errors->has('description'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('permissions') ? ' has-error' : '' }}">
                                        <label for="permission" class="col-md-4 control-label">Permissions</label>

                                        <div class="col-md-6">
                                            <ul class="icheck-list">
                                                @foreach ($permissions as $permission)
                                                <li>
                                                    <input type="checkbox" class="icheck" name="permissions[]" id="checkbox-{{$permission->id}}" value="{{$permission->id}}" <?php if (in_array($permission->id, $rolePermissions)) echo "checked"; ?>>
                                                    <label for="checkbox-{{$permission->id}}">{{$permission->display_name}}</label>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @if ($errors->has('permissions'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('permissions') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group mt30">
                                        <div class="col-md-8 col-md-offset-4">

                                            <button class="btn btn-green" type="submit">Update</button>
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
</div>
</div>



@endsection