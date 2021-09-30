
@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "#", 'text' =>  'Admin'),
        array('link' => "", 'text' =>  'User Roles')
    );
    breadcrum($breadcrum);
    $specific_client_id = 0;
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
                                        <h1 class="mt10">User Roles</h1>
                                    </div>
                                    {{-- <div class="col-md-5">
                                        <div class="btn-group btn-sales-all">
                                            <div class="update_client_by_location w130">
                                                <select class="select2 auto-submit" name="status" id="status">
                                                    <option value="active">Active Users</option>
                                                    <option value="inactive">Deactivated Users</option>
                                                    <option value="all">All Users</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        @if(auth()->user()->can('edit-permission-roles'))
                                        <div class="col-xs-6 col-sm-3 col-md-3">
                                            <a href="{{route('edit.permissions.roles')}}" class="btn btn-green pull-right tpv-user-modal"
                                               data-original-title="Add TPV User" data-toggle="modal" data-type="new">
                                                Edit
                                            </a>
                                        </div>
                                    @endif
                                    </div> --}}
                                    <form action="{{route('edit.external.permissions.roles')}}" method="POST">
                                        @csrf
                                    <div class="col-md-4">
                                        <div class="btn-group pull-right btn-sales-all">
                                            <div class="update_client_by_location w130">
                                                <select class="select2 auto-submit client_id" name="client_id" id="client_id">
                                                    @foreach ($clients as $client)
                                                    <option value="{{$client['id']}}" @if($client['id'] == config('constants.DEFAULTS_CLIENT_ID_PERMISSION')) selected @endif>{{$client['name']}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                              
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        @if(auth()->user()->can('edit-permission-roles'))
                                        <div class="col-xs-6 col-sm-3 col-md-3">
                                            {{-- <a href="{{route('edit.external.permissions.roles',['client_id' => $specific_client_id])}}" class="btn btn-green"
                                               data-original-title="Add TPV User" data-toggle="modal" data-type="new">
                                                Edit
                                            </a> --}}
                                            <button type="submit" id="btn-save"
                                            class="btn btn-green">Edit</button>
                                        </div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
                                       @endif
                                    </div>
                                </form>
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
                                    <div class="table-responsive">
                                        <table class="table" id="permission-table">
                                            <thead>
                                            <tr class="acjin">
                                                <th>Permissions</th>
                                                @foreach($roles_array as $role)
                                                    <th>{{ $role['display_name'] }}</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permissions as $key => $permissionItems)
                                                
                                                    <tr class="permission-group">
                                                        <th colspan="{{count($roles_array)+1}}"> {{$key}} </th>
                                                    </tr>
                                                    @foreach($permissionItems as $permission)
                                                        @if($permission->name != 'generate-sales-activity-report' && $permission->name != 'export-sales-activity-report' && $permission->name != 'filter-sales-activity-report')
                                                        <tr>
                                                            <td>{{ $permission->display_name }}</td>
                                                            {!! getRolesClientInTD($permission_role_specific,$roles_array,$permission->id,$permission->roles) !!}
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
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
@push('scripts')

<script>
    var clientId = $(location).attr("href").split('/').pop();
    
    if($.isNumeric(clientId)){
        $('#client_id').val(clientId);
    }
    
    $('#client_id').change(function() {
        var client_id = $(this).val();
        var url =  "{{route('all.external.permissions', '')}}"+"/"+client_id;
        window.location.href = url;
        $('#client_id').val(client_id);
        
    });
</script>

@endpush
