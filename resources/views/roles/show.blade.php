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
                    <div class="col-sm-4 col-xs-6">
                        <div class="tile-stats tile-red">
                            <div class="icon"><i class="fa fa-tag"></i></div>
                            <h3>Name</h3>
                            <h4 class="white-text permission-content equal-height-content">{{ $role->display_name }}</h4>
                        </div>

                    </div>
                    <div class="col-sm-4 col-xs-6">
                        <div class="tile-stats tile-green ">
                            <div class="icon"><i class="fa fa-info"></i></div>
                            <h3>Description</h3>
                            <h4 class="white-text permission-content equal-height-content">{{ $role->description }}</h4>
                        </div>
                    </div>
                    <div class="clear visible-xs"></div>

                    <div class="col-sm-4 col-xs-6">

                        <div class="tile-stats tile-aqua">
                            <div class="icon"><i class="fa fa-key"></i></div>
                            <h3>Permissions</h3>
                            <div class="white-text permission-content equal-height-content">
                                @if(!empty($permissions))
                                @foreach($permissions as $permission)
                                <label class="label label-success">{{ $permission->display_name }}</label>
                                @endforeach
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-12 col-xs-12">
                        <div class="btnintable bottom_btns">
                            <div class="btn-group">
                                <a class="btn btn-green" href="{{ route('roles.edit',$role->id) }}">Edit</a>
                                <a class="btn btn-red deleterole" href="javascript:void(0)" data-roleid="{{ $role->id }}" id="delete-role-{{ $role->id }}" data-toggle="tooltip" data-target="#DeleteRole" data-role="{{ $role->display_name }}" data-placement="top" data-container="body" title="" data-original-title="Delete Role">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    @include('roles.deletepopup')
    @endsection