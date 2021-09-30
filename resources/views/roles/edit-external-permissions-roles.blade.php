@extends('layouts.admin')
@section('content')

<?php
    $breadcrum = array(
        array('link' => "#", 'text' =>  'Admin'),
        array('link' => route('all.permissions'), 'text' =>  'User Roles'),
        array('link' => "", 'text' =>  'Edit User Roles')
    );
    breadcrum($breadcrum);
    ?>
<form action="{{ route('update.external.permissions.roles')}}" method="POST">
    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="client-bg-white">

                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="mt10">Edit User Roles</h1>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="btn-group pull-right btn-sales-all">
                                            <div class="update_client_by_location w130">
                                                <select class="select2 auto-submit" name="client_id" id="client_id">
                                                    @foreach ($clients as $client)
                                                    <option value="{{$client['id']}}"
                                                        {{ $client_id == $client['id'] ? 'selected' : '' }}>
                                                        {{$client['name']}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="btn-save"
                                            class="btn btn-green pull-right">Save</button>
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
                                    <div class="table-responsive">

                                        <table class="table" id="permission-table">
                                            <thead>
                                                <tr class="acjin">
                                                    <th>Permissions</th>
                                                    @foreach($roles_array as $role)
                                                    @if($role['name'] != 'admin')
                                                    <th>{{ $role['display_name'] }}</th>
                                                    @endif
                                                    @endforeach
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($permissions as $key => $permissionItems)
                                                <tr class="permission-group">
                                                    <th colspan="{{count($roles_array)+1}}"> {{$key}} </th>
                                                </tr>
                                                @foreach($permissionItems as $permission)
                                                <?php
                                                            $disabledPermissions = ['dashboard', 'edit-permission-roles', 'view-user-roles', 'all-clients', 'delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user',  'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent'];
                                                            if (in_array($permission->name, $disabledPermissions)) {
                                                                $disabled = 'disabled';
                                                            } else {
                                                                $disabled = '';
                                                            }
                                                        ?>
                                                <tr>
                                                    <td>{{ $permission->display_name }}</td>
                                                    {!!
                                                    getPermissionsClientInTDForEdit($permission_role_specific,$permission->accessLevels,$roles_array,$permission->id,$disabled,$permission->roles)
                                                    !!}
                                                </tr>
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
    <div class="modal fade confirmation-model" id="confirm-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mt15 text-center mb15">
                        <?php echo getimage('/images/alert-danger.png') ?>
                        <p class="logout-title">Are you sure?</p>
                    </div>
                </div>
                <div class="modal-footer pd0">
                    <div class="btnintable bottom_btns pd0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-green">Yes</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@push('scripts')
<script>
    $('#client_id').change(function() {
        var client_id = $(this).val();
        var url =  "{{route('get.external.permissions.roles', '')}}"+"/"+client_id;
        window.location.href = url;
    });
    
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn-save').on('click', function (e) {

            $('#confirm-modal').modal();
        });

    });
</script>

@endpush