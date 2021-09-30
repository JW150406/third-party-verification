@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "#", 'text' => 'Settings'),
        array('link' => "", 'text' => 'Workspace')
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
                                        <h1 class="mt10">Workspace</h1>
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
                                </div>
                                <div class="sales_tablebx mt30">
                                    <form action="{{route('settings.saveWorkspace')}}" method="post" data-parsley-validate>
                                    @csrf
                                    <div class="form-group {{ $errors->has('workspace_id') ? ' has-error' : '' }}">
                                        <label for="workspace_id" class="{{ $star }}">Workspace Id</label>
                                        <input id="workspace_id" maxlength="100" type="text" value="{{ old('workspace_id', array_get($workspace, 'workspace_id'))}}"
                                               class="form-control required" name="workspace_id" autofocus="" data-parsley-required='true' maxlength="255">
                                        
                                        @if ($errors->has('workspace_id'))
                                            <span class="help-block">
                                            {{ $errors->first('workspace_id') }}
                                        </span>
                                        @endif

                                    </div>
                                    <div class="form-group  {{ $errors->has('workspace_name') ? ' has-error' : '' }}">
                                        <label for="workspace_name" class="{{ $star }}">Workspace Name</label>
                                        <input id="workspace_name" maxlength="100" type="text" value="{{ old('workspace_name',array_get($workspace, 'workspace_name'))}}"
                                               class="form-control required" name="workspace_name" autofocus="" data-parsley-required='true'  maxlength="255">
                                        
                                        @if ($errors->has('workspace_name'))
                                            <span class="help-block">{{ $errors->first('workspace_name') }}
                                        </span>
                                        @endif

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
