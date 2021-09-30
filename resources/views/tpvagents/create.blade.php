@extends('layouts.admin')
@section('content')
<ol class="breadcrumb bc-3">
   <li>
      <a href="{{route('dashboard')}}"><i class="fa fa-home"></i>Home</a>
   </li>
   <li>
      <a href="{{route('tpvagents.index') }}">TPV Agents</a>
   </li>
    <li class="active">
      <strong>Add New Agent</strong>
   </li>
</ol>
<div class="clearfix"></div>
<h2>New Agent Info</h2>
<br />
  
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                <div class="panel-heading">
							<div class="panel-title">
								Agent Detail
							</div>
				 	</div> 

                    <div class="panel-body">
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

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('tpvagents.index') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{  $errors->has('first_name')  ? ' has-error' : '' }} ">
                            <label for="name" class="col-md-4 control-label">First Name</label>
                                <div class="col-md-6">
                                        <input id="first_name" autocomplete="off" type="text" class="form-control" name="first_name" value="{{ old('city') }}"
                                            required placeholder="First Name"  value="{{ old('first_name')}}" >

                                        @if ($errors->has('first_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                             </div>
                             <div class="form-group{{  $errors->has('last_name')  ? ' has-error' : '' }} ">
                             <label for="name" class="col-md-4 control-label">Last Name</label>
                                <div class="col-md-6">
                                        <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name" value="{{ old('state') }}"
                                             placeholder="Last Name" value="{{ old('last_name')}}" >

                                        @if ($errors->has('last_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                             </div>
                             <div class="clearfix"></div>
                            
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">Email:</label>

                                <div class="col-md-6">
                                    <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email ID"                 required     >

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-md-4 control-label">Twilio:</label>

                                <div class="col-md-3">
                                <select name="twilio_ids[workspace_id][]" id="workspace_select" class="selectmenu">
                                 <option value="">Select</option>
                                 @foreach($client_workspaces as $clientworkspace)
                                 <option value="{{ $clientworkspace->workspace_id }}">{{ $clientworkspace->workspace_name }}</option>
                                 @endforeach
                                </select>
                                    <!-- <input  type="text" class="form-control" name="" value="" placeholder="Workspace ID"> -->
                                </div>
                                 
                                <div class="col-md-3">
                                    <input  type="text" id="twilio_worker_id" class="form-control" name="twilio_ids[worker_id][]" value="" placeholder="Worker ID">
                                </div>
                                <span class="inline-block" style="margin-top:5px"><a href="#" class="badge badge-success badge-roundless create-twilio-record"><i class="fa fa-plus"></i> </a></span>
                            </div>
                            <div class="appendnewtwilioids">
                            </div>
                            


                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Save
                                    </button>

                                    <a class="btn btn-link" href="{{ route('tpvagents.index') }}">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
@endsection