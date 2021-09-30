@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
  array('link' => "", 'text' =>  'Team Management')
);
?>
{{breadcrum ($breadcrum)}}

<div class="tpv-contbx">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12">
        
          <div class="cont_bx3">
          <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="client-bg-white">
           
              <h1>TPV Members</h1>
            
            <?php if (Auth::user()->can(['user-create'])) { ?>
              <div class="top_sales">
                <div class="tpvbtn text-right">


                  <button class="btn btn-green" type="submit" data-toggle="modal" data-target="#addtpvmember">New TPV User</button>

                </div>
              <?php } ?>
              <div class="clearfix"></div>

              @if ($message = Session::get('success'))
              <div class="tpvbtn"> 
                  <div class="alert alert-success">
                    <p>{{ $message }}</p>
                  </div>
              </div>
              @endif

              <?php if (Auth::user()->can(['user-list'])) { ?>
                <div class="sales_tablebx mt30">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr class="acjin">
                          <th>No</th>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Role</th>
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
                          <td class="{{$second_and_middle_td_class}}">{{ $user->userid }}</td>
                          <td class="{{$second_and_middle_td_class}}">
                            <a href="{{ route('teammembers.show',$user->id) }}">{{ $user->first_name }} {{ $user->last_name }}</a></td>
                          <td class="{{$second_and_middle_td_class}}">{{ $user->email }}</td>
                          <td class="{{$second_and_middle_td_class}}">
                            @if(!empty($user->roles))
                            @foreach($user->roles as $role)
                            {{ $role->display_name }}
                            @endforeach
                            @endif
                          </td>
                          <td class="{{$first_last_td_class}}">

                            <div class="btn-group">
                              {{ view_btn( route('teammembers.show',$user->id) , 'View user info') }}

                              @if(Auth::user()->can(['user-update']))
                              {{ edit_btn( route('teammembers.edit',$user->id) , 'Edit user info') }}
                              @endif

                              @if(Auth::user()->can(['user-delete']))

                              <?php delete_btn('javascript:void(0)', 'Delete User',   'deleteuser',  'data-uid="' . $user->id . '" id="delete-user-' . $user->id . '" data-teamuser="' . $user->first_name . ' ' . $user->last_name . '" ') ?>

                              @endif

                            </div>
                          </td>
                        </tr>
                        @endforeach
                        @endif
                        @if(count($users)==0)
                        <tr class="list-users">
                          <td colspan="6" class="text-center">No Record Found</td>
                        </tr>
                        @endif


                      </tbody>
                    </table>
                    <div class="pagination-wrapper">
                      {!! $users->render() !!}
                    </div>
                  </div>
                </div>
              </div>  
          </div>
        <?php } ?>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>


@include('teammembers.deleteteamuserpoup')
@include('teammembers.addnewpopup')

@endsection