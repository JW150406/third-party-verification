@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(

  array('link' => route('admin.dispositionslist'), 'text' =>  'Dispositions'),
  array('link' => "", 'text' =>  'Edit'),
);
breadcrum($breadcrum);
?>
<div class="tpv-contbx edit-agentinfo">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="cont_bx3">
          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="client-bg-white">
              <h1>Edit Disposition Info</h1>
              <div class="sales_tablebx">
                <!-- Nav tabs -->
                <!-- Tab panes -->
                <div class="tab-content">

                  <!--agent details starts-->

                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">

                      <div class="agent-detailform">
                        <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
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
                          @if ($message = Session::get('success'))
                          <div class="alert alert-success">
                            <p>{{ $message }}</p>
                          </div>
                          @endif
                          <form enctype="multipart/form-data" role="form" method="POST" action="">
                            {{ csrf_field() }}
                            <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                              <label for="selectdispositiontype">Category</label>
                              <select name="type" id="selectdispositiontype" class="selectmenu" required>
                                <option value="">Select</option>
                                <option value="decline" @if($desposition->type == 'decline') selected @endif >Decline Lead</option>
                                <option value="customerhangup" @if($desposition->type == 'customerhangup') selected @endif>Call disconnected</option>
                              </select>
                              @if ($errors->has('type'))
                              <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                              </span>
                              @endif
                            </div>
                            <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                              <label for="name"></label>
                              <input id="name" autocomplete="off" type="text" class="form-control" name="description" value="{{ $desposition->description }}" required placeholder="Description">
                              @if ($errors->has('description'))
                              <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                              </span>
                              @endif
                            </div>
                            <div class="form-group radio-btns checkbx">
                              <label class="checkbx-style">Allow Cloning
                                <input id="allow_cloning" autocomplete="off" type="checkbox" name="allow_cloning" @if($desposition->allow_cloning == 'true' ) checked @endif >
                                <span class="checkmark"></span>
                              </label>
                              @if ($errors->has('allow_cloning'))
                              <span class="help-block">
                                <strong>{{ $errors->first('allow_cloning') }}</strong>
                              </span>
                              @endif
                            </div>

                            <div class="btnintable bottom_btns">
                              <div class="btn-group">
                                <button class="btn btn-green" type="submit">Update</button>

                              </div>
                            </div>
                          </form>
                        </div>
                      </div>

                    </div>
                  </div>

                  <!--agent details ends-->

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