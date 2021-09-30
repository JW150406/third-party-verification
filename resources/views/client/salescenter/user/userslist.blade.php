

@extends('layouts.admin')
@section('content')
<?php
   $breadcrum = array() ;
   if( Auth::user()->access_level =='tpv')
   {
       $breadcrum[] = array('link' => route('client.findsalecenter'), 'text' =>  'Find Sales Center' );
       $breadcrum[] = array('link' =>  route('client.findsalecenter',['client' => $client->id]) , 'text' =>  $client->name );
   }else{
       $breadcrum[] = array('link' =>  route('client.salescenters',$client->id) , 'text' =>  'Sales Centers' );
   }
   $breadcrum[] = array('link' => route('client.salescenter.show',['id' => $client->id, 'salescenter_id' => $salescenter->id ]), 'text' => $salescenter->name);
   $breadcrum[] = array('link' => '', 'text' =>'Users');
   breadcrum ($breadcrum)
   ?>
<div class="tpv-contbx">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <h1>Users</h1>
               </div>
               <div class="tpvbtn">
                  <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                     <a class="btn btn-green" href="{{ route('client.salescenter.adduser',['id' => $client_id, 'salescenter_id' =>$salescenter_id  ]) }}"  data-toggle="modal" data-target="#addsalescenteruser" >Add   @if(Auth::user()->access_level =='tpv' || Auth::user()->access_level =='client' )  Sales Center @endif User<span class="add"><?php echo getimage('images/add.png') ?></span></a>
                  </div>
                  <div class="clearfix"></div>
                  @if ($message = Session::get('success'))
                  <div class="tpvbtn">
                    <div class="col-xs-12 col-sm-12 col-md-12 ">
                        <div class="alert alert-success">
                          <p>{{ $message }}</p>
                        </div>
                    </div>
                  </div>
                  @endif
                  <div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-6 col-md-offset-6 sor_fil">
                     <!-- <div class="col-xs-12 col-sm-4 col-md-4 sort">
                        <div class="dropdown">
                        			<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{ asset('images/sort.png') }}"/></span>Sort by
                        			<span class="drop_blk"><img src="{{ asset('images/dropicon_blk.png')}}"/></span></button>
                        			<ul class="dropdown-menu">
                        			  <li><a href="#">WTD</a></li>
                        			  <li><a href="#">YTD</a></li>
                        			  <li><a href="#">WTD</a></li>
                        			</ul>
                        </div>
                        </div> -->
                     <!-- <div class="col-xs-12 col-sm-4 col-md-4 filter">
                        <div class="dropdown">
                        			<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{ asset('images/filter.png')}}"/></span>Filters
                        			<span class="drop_blk"><img src="{{ asset('images/dropicon_blk.png')}}"/></span></button>
                        			<ul class="dropdown-menu">
                        			  <li><a href="#">WTD</a></li>
                        			  <li><a href="#">YTD</a></li>
                        			  <li><a href="#">WTD</a></li>
                        			</ul>
                        </div>
                        </div> -->
                     <!-- <div class="col-xs-12 col-sm-4 col-md-4 search">
                        <div class="search-container">
                        	<form action="">
                        	   <button type="submit"><img src="{{ asset('images/search.png')}}"/></button>
                        	   <input type="text" placeholder="Search" name="search">
                        	</form>
                        </div>
                        </div> -->
                  </div>
               </div>
               <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                  <div class="table-responsive">
                     <table class="table responsive">
                        <thead>
                           <tr class="heading">
                              <th>No</th>
                              <th>Name</th>
                              <th>Email</th>

                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $i = 0; ?>
                           @foreach ($center_users as $key => $center_user)
                           <?php if($i % 2 == 0){
                              $first_last_td_class = "light_c";
                              $second_and_middle_td_class = "white_c";
                              }else{
                              $first_last_td_class = "dark_c";
                              $second_and_middle_td_class = "grey_c";
                              }
                              ?>
                           <tr>
                              <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                              <td class="{{$second_and_middle_td_class}}"><a href="{{ route('client.salescenter.user.show',['client_id' => $client_id, 'salescenter_id'=>$center_user->salescenter_id, 'userid' =>$center_user->id  ]) }}">{{ $center_user->first_name }} {{ $center_user->last_name }}</a></td>
                              <td class="{{$second_and_middle_td_class}}">{{ $center_user->email }}</td>

                              <td class="{{$first_last_td_class}}">
                                 <div class="btn-group">
                                    <a class="btn"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View User Info" href="{{ route('client.salescenter.user.show',['client_id' => $client_id, 'salescenter_id'=>$center_user->salescenter_id, 'userid' =>$center_user->id  ]) }}" role="button"><?php echo getimage("images/view.png"); ?> </a>
                                    <a class="btn"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit User"  href="{{ route('client.salescenter.user.edit',['client_id' => $client_id, 'salescenter_id'=>$center_user->salescenter_id, 'userid' =>$center_user->id  ]) }}" role="button"><?php echo getimage("images/edit.png"); ?> </a>
                                    <?php if($center_user->status=='active'){ ?>
                                    <a class="deactivate-salescenteruser btn"
                                       href="javascript:void(0)"
                                       data-toggle="tooltip"
                                       data-placement="top" data-container="body"
                                       title=""
                                       data-original-title="Deactivate User"
                                       data-uid="{{ $center_user->id }}"
                                       id="delete-salescenteruser-{{ $center_user->id }}"
                                       data-salescenteruser="{{ $center_user->first_name }} {{ $center_user->last_name }}">
                                    <?php echo getimage("images/activate_new.png"); ?></a>
                                    <?php } else {?>
                                    <a
                                       class="activate-salescenteruser btn"
                                       href="javascript:void(0)"
                                       data-toggle="tooltip"
                                       data-placement="top" data-container="body"
                                       title=""
                                       data-original-title="Activate User"
                                       data-uid="{{ $center_user->id }}"
                                       id="delete-salescenteruser-{{ $center_user->id }}"
                                       data-salescenteruser="{{ $center_user->first_name }} {{ $center_user->last_name }}">
                                    <?php echo getimage("images/deactivate_new.png"); ?></a>
                                    <?php }?>
                                 </div>
                              </td>
                           </tr>
                           @endforeach
                           @if(count($center_users)==0)
                           <tr class="list-users">
                              <td colspan="4" class="text-center">No Record Found</td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
                     <div class="btnintable bottom_btns">
                         {!! $center_users->render() !!}
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@include('client.salescenter.user.salescenteruserspoup')
<div class="team-addnewmodal">
<div class="modal fade" id="addsalescenteruser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
      </div>
   </div>
</div>
@endsection
