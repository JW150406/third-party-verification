@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
    array('link' => route('tpvagents.index'), 'text' =>  'Team Agents'),
    array('link' => "", 'text' =>  $user->first_name),
);
breadcrum($breadcrum);
?>


<div class="tpv-contbx edit-agentinfo">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                        <div class="client-bg-white">
                            <h1>Edit Agent Info</h1>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#agentdetail" aria-controls="home" role="tab" data-toggle="tab">Agent Details</a></li>
                                @if($user->access_level == 'tpvagent')
                                <li role="presentation"><a href="#twilioseting" aria-controls="profile" role="tab" data-toggle="tab">Twilio Settings</a></li>
                                @endif
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">

                                <!--agent details starts-->

                                <div role="tabpanel" class="tab-pane active" id="agentdetail">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">

                                            <div class="agent-detailform">
                                                <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                                    @if ($message = Session::get('success'))
                                                    <div class="alert alert-success">
                                                        <p>{{ $message }}</p>
                                                    </div>
                                                    @endif
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
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('tpvagents.update', $user->id) }}">
                                                        {{ csrf_field() }}
                                                        {{ method_field('POST') }}

                                                        <div class="form-group{{  $errors->has('first_name')  ? ' has-error' : '' }} ">
                                                            <label for="first_name">First name</label>
                                                            <input id="first_name" autocomplete="off" type="text" class="form-control required" name="first_name" required placeholder="First Name" value="{{$user->first_name}}">
                                                            <?php echo  getFormIconImage("images/form-name.png") ?>
                                                            @if ($errors->has('first_name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('first_name') }}</strong>
                                                            </span>
                                                            @endif

                                                        </div>

                                                        <div class="form-group{{  $errors->has('last_name')  ? ' has-error' : '' }} ">
                                                            <label for="last_name">Last name</label>
                                                            <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{$user->last_name}}">
                                                            <?php echo  getFormIconImage("images/form-name.png") ?>
                                                            @if ($errors->has('last_name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('last_name') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>

                                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                                            <label for="email">Email</label>
                                                            <input id="email" type="email" class="form-control required" name="email" value="{{$user->email}}" autocomplete="off" required placeholder="Email">
                                                            <?php echo  getFormIconImage("images/form-email.png") ?>
                                                            @if ($errors->has('email'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('email') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                                            <label for="exampleInputPassword1">Password</label>
                                                            <input id="password" type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
                                                            <?php echo  getFormIconImage("images/form-pass.png") ?>
                                                            @if ($errors->has('password'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('password') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                            <label for="password_confirmation">Confirm password</label>
                                                            <input id="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" autocomplete="off">
                                                            <?php echo  getFormIconImage("images/form-pass.png") ?>
                                                            @if ($errors->has('password_confirmation'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="btnintable bottom_btns">
                                                            <div class="btn-group">
                                                                <button class="btn btn-green" type="submit">Update</button>
                                                                <a class="btn btn-red" href="{{ route('tpvagents.index') }}">Cancel</a>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div>

                                <!--agent details ends-->

                                <!--twilio setting content starts-->

                                <div role="tabpanel" class="tab-pane" id="twilioseting">
                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('edit-twiliosettings') }}">
                                        {{ csrf_field() }}
                                        {{ method_field('post') }}
                                        <input type="hidden" name="userid" value="{{$user->id}}">
                                        <div class="table-responsive mt30">
                                            <table class="table twilio-workersid-detail">
                                                <thead>
                                                    <tr class="heading ">
                                                        <th>Workspace Name</th>
                                                        <th>Worker ID</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="setting_rows">
                                                        <td class="light_c">

                                                            <select id="workspace_select" class="selectmenu">
                                                                <option value="">Select</option>
                                                                @foreach($client_workspaces as $clientworkspace)
                                                                <option value="{{ $clientworkspace->workspace_id }}">{{ $clientworkspace->workspace_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="white_c">

                                                            <input type="text" value="" class="form-control" id="twilio_worker_id" style="height:42px">
                                                        </td>
                                                        <td class="light_c">
                                                            <button class="btn btn-green addnew addnew-twilio-record" type="submit">Add New</button>

                                                        </td>
                                                    </tr>

                                                    <?php $i = 0; ?>
                                                    @foreach($twilio_ids as $twiliodetail)
                                                    <?php if ($i % 2 == 0) {
                                                        $first_last_td_class = "dark_c";
                                                        $second_and_middle_td_class = "grey_c";
                                                    } else {
                                                        $first_last_td_class = "light_c";
                                                        $second_and_middle_td_class = "white_c";
                                                    }
                                                    ?>
                                                    <tr class="setting_{{$twiliodetail->id}} setting_rows">
                                                        <td class="{{$first_last_td_class}}">
                                                            @foreach($client_workspaces as $clientworkspace)
                                                            @if($twiliodetail->workspace_id == $clientworkspace->workspace_id ) {{ $clientworkspace->workspace_name }} @endif
                                                            @endforeach


                                                            <input type="hidden" name="twilio_ids[workspace_id][]" value="{{ $twiliodetail->workspace_id }}">

                                                        </td>
                                                        <td class="{{$second_and_middle_td_class}}">
                                                            {{ $twiliodetail->twilio_id }}
                                                            <input type="hidden" name="twilio_ids[worker_id][]" value="{{ $twiliodetail->twilio_id }}">
                                                        </td>
                                                        <td class="{{$first_last_td_class}}">
                                                            <button class="btn btn-red deletetiwilioid" type="button" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Record" data-id="{{ $twiliodetail->id }}" id="delete-client-{{ $twiliodetail->id }}">Delete</button>


                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                    @endforeach



                                                </tbody>
                                            </table>

                                            <div class="btnintable">
                                                <div class="btn-group">
                                                    <button class="btn btn-green" type="submit">Update</button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                                <!--twilio setting content ends-->

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.twiliosettingspoup')
@endsection
