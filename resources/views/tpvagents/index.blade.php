@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
  array('link' => "", 'text' =>  'TPV Agents')
);
?>
{{breadcrum ($breadcrum)}}

<div class="tpv-contbx">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="cont_bx3">
          <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
            <div class="client-bg-white">
              <div class="row">
                <div class="col-md-6 cl-sm-6">
                  <h1>TPV Agents</h1>
                </div>
                <div class="col-md-6 cl-sm-6">
                  <?php if (Auth::user()->can(['create-update-tpv-agent'])) { ?>
                    <div class="top_sales">
                      <button class="btn btn-green pull-right" type="submit" data-toggle="modal" data-target="#addtpvagent">New Agent</button>
                    </div>
                  <?php } ?>
                </div>
              </div>
              <div class="clearfix"></div>
              @if ($message = Session::get('success'))
              <div class="alert alert-success">
                <p>{{ $message }}</p>
              </div>
              @endif

              <div class=" sales_tablebx mt30">
                <?php if (Auth::user()->can(['view-tpv-agents'])) { ?>
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr class="acjin">
                          <th>No</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th class="visi-hidden">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(count($users)> 0)
                        <?php $i = 0; ?>
                        @foreach ($users as $key => $user)
                        <?php if ($i % 2 == 0) {
                          $first_last_td_class = "dark_c";
                          $second_and_middle_td_class = "grey_c";
                        } else {
                          $first_last_td_class = "light_c";
                          $second_and_middle_td_class = "white_c";
                        }
                        ?>
                        <tr class="list-users">
                          <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                          <td class="{{$second_and_middle_td_class}}"> <a href="{{ route('tpvagents.show',$user->id) }}">{{ $user->first_name }} {{ $user->last_name }}</a></td>

                          <td class="{{$second_and_middle_td_class}}">{{ $user->email }}</td>

                          <td class="{{$first_last_td_class}}">

                            <div class="btn-group">
                              <?php view_btn(route('tpvagents.show', $user->id), 'View user info') ?>

                              @if(Auth::user()->can(['create-update-tpv-agent']))
                              <?php edit_btn(route('tpvagents.edit', $user->id), 'Edit user info') ?>
                              @endif

                              @if(Auth::user()->can(['delete-tpv-agent']))

                              <?php delete_btn('javascript:void(0)', 'Delete User',   'deleteuser',  'data-uid="' . $user->id . '" id="delete-user-' . $user->id . '" data-teamuser="' . $user->first_name . ' ' . $user->last_name . '" ') ?>

                              @endif

                            </div>
                          </td>
                        </tr>
                        @endforeach
                        @endif
                        @if(count($users)==0)
                        <tr class="list-users">
                          <td colspan="4" class="text-center">No Record Found</td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                    <div class="clearfix"></div>
                    <div class="pagination-wrapper">
                      {!! $users->render() !!}
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



  @include('tpvagents.deletetpvagentpopup')
  @include('tpvagents.addnewtpvagentpopup')
  @endsection