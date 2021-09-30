
@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "#", 'text' =>  'Admin'),
        array('link' => "", 'text' =>  'User Roles')
    );
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
                                        <h1 class="mt10">User Roles</h1>
                                    </div>
                                    @if(auth()->user()->can('edit-permission-roles'))
                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                            <a href="{{route('edit.permissions.roles')}}" class="btn btn-green pull-right mr15 tpv-user-modal"
                                               data-original-title="Add TPV User" data-toggle="modal" data-type="new">
                                                Edit
                                            </a>
                                        </div>
                                    @endif
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
                                                            {!! getRolesInTD($permission->roles,$roles_array) !!}
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

@endpush
