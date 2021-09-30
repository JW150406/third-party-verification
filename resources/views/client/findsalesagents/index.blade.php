@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(array('link' => '', 'text' => 'Find Sales Agent')) ;
breadcrum ($breadcrum)
?>


<div class="tpv-contbx" style="padding-bottom:0">
			<div class="container">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3 salesagent_contbx">
                            <h1>Find Sales Agent</h1>
                            <?php if(Auth::user()->hasPermissionTo('view-salesagents') || Auth::user()->access_level =='client'){ ?>
                            <form class="form-horizontal get-salesagents"  enctype="multipart/form-data" role="form" method="GET" action="">
                            <?php }?>
                             {{ csrf_field() }}
						          <div class="row">
                             @if( Auth::user()->access_level =='tpv')
                             <div class="col-xs-12 col-sm-3 col-md-3">
									<p>Select Client</p>
								 	<select class="selectmenu select-box-admin selectclient" name="client" id="selectclient">
                                        <option value="">Select</option>
                                        @if(count($clients)>0)
                                           @foreach($clients as $client)
                                              <option value="{{$client->id}}"  <?php if($client_id == $client->id) echo "selected='selected'"; ?>>{{$client->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                 </div>

                         @endif
                         @if(Auth::user()->access_level =='client')
                           <input type="hidden"  id="selectclient" name="client" value="{{$client_id}}"/>
                         @endif

                            <div class="col-xs-12 col-sm-3 col-md-3">
                                    <p>Sale Center</p>
                                    <div class="select-center-wrapper">
									<select class=" select-box-admin selectcenter" name="salecenter" id="salecenters">
                                        <option value="">Select</option>
                                        @if(count($sale_centers)>0)
                                           @foreach($sale_centers as $salecenter)
                                              <option value="{{$salecenter->id}}"  <?php if($salecenter_id == $salecenter->id) echo "selected='selected'"; ?>>{{$salecenter->name}}</option>
                                           @endforeach
                                        @endif

                                    </select>
                                    </div>
								 </div>
								 <div class="col-xs-12 col-sm-3 col-md-3">
                                    <p>Location</p>
                                    <div class="select-locaion-wrapper">
									<select class="selectmenu select-box-admin selectlocation" name="location" id="location">
                                        <option value="">Select</option>
                                        @if(count($locations)>0)
                                           @foreach($locations as $location)
                                              <option value="{{$location->id}}"  <?php if($location_id == $location->id) echo "selected='selected'"; ?>>{{$location->name}}</option>
                                           @endforeach
                                        @endif

                                    </select>
								   </div>
								 </div>
								 <div class="col-xs-12 col-sm-3 col-md-3">
									<button class="btn btn-green" type="submit">Submit<span class="add"><?php echo getimage("images/update_w.png"); ?></span></button>
								 </div>
                             </div>
                             <?php if(Auth::user()->hasPermissionTo('view-salesagents') || Auth::user()->access_level =='client'){ ?>
                            </form>
                             <?php } ?>
						</div>
				</div>
		</div>
	</div>



  @if($client_id != "")
  <div class="tpv-contbx">
   <div class="container">



        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
            @if( $salecenter_id!="")
            <?php if(Auth::user()->hasPermissionTo('create-salesagents') || Auth::user()->access_level =='client'){ ?>
             <a style="margin-bottom:15px;" class="btn btn-green" href="{{ route('client.salescenter.addsalesagent',['client_id' => $client_id, 'salescenter_id' =>$salecenter_id ,'location' => $location_id ]) }}"  data-toggle="modal" data-target="#addnewsalesagent" > Add New Sales Agent<span class="add"><?php echo getimage('images/add.png') ?></span></a>
            <?php } ?>
             @endif
           @if ($message = Session::get('success'))
                         <div class="tpvbtn">
                              <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="alert alert-success">
                                        <p>{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
          <div class="clearfix"></div>

                  <div class="table-responsive">
                     <table class="table responsive">
                        <thead>
                           <tr class="heading">
                                <th>No</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $i = 0; ?>
                           @foreach ($users as $key => $agent_user)
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
                              <td class="{{$second_and_middle_td_class}}">{{ $agent_user->userid }}</td>
                              <td class="{{$second_and_middle_td_class}}"><a href="{{ route('client.salescenter.salesagent.show',['client_id' => $client_id, 'salescenter_id'=>$agent_user->salescenter_id, 'userid' =>$agent_user->id  ]) }}" >{{ $agent_user->first_name }} {{ $agent_user->last_name }}</a></td>
                              <td class="{{$second_and_middle_td_class}}">{{ $agent_user->email }}</td>
                              <td class="{{$first_last_td_class}}">
                                 <div class="btn-group">
                                 <?php if(Auth::user()->hasPermissionTo('view-salesagents') || Auth::user()->access_level =='client' ){ ?>
                                    <a class="btn"
                                    href="{{ route('client.salescenter.salesagent.show',['client_id' => $client_id, 'salescenter_id'=>$agent_user->salescenter_id, 'userid' =>$agent_user->id  ]) }}"
                                    data-toggle="tooltip"
                                    data-placement="top" data-container="body" title=""
                                    data-original-title="View Agent"
                                    role="button"><?php echo getimage("images/view.png"); ?> </a>
                                 <?php }?>
                                 <?php if(Auth::user()->hasPermissionTo('update-salesagent') || Auth::user()->access_level =='client'){ ?>
                                    <a class="btn"
                                        href="{{ route('client.salescenter.salesagent.edit',['client_id' => $client_id, 'salescenter_id'=>$agent_user->salescenter_id, 'userid' =>$agent_user->id,'client'=>$client_id,  'salecenter'=>$salecenter_id,'location' => $location_id ,'ref'=>'findagent']) }}"
                                        data-toggle="tooltip"
                                        data-placement="top" data-container="body"
                                        title=""
                                        data-original-title="Edit Agent Info"
                                        role="button"><?php echo getimage("images/edit.png"); ?> </a>
                                        <?php }?>
                                        <?php if(Auth::user()->hasPermissionTo('delete-salesagent') || Auth::user()->access_level =='client' ){ ?>
                                                <?php if($agent_user->status=='active'){ ?>
                                                <a  class="deactivate-salescentersaleuser btn"
                                                   href="javascript:void(0)"
                                                   data-toggle="tooltip"
                                                   data-placement="top" data-container="body"
                                                   title=""
                                                   data-original-title="Deactivate Agent"
                                                   data-uid="{{ $agent_user->id }}"
                                                   id="delete-salescentersaleuser-{{ $agent_user->id }}"
                                                   data-salescentersaleuser="{{ $agent_user->first_name }} {{ $agent_user->last_name }}" data-sid="{{ $agent_user->salescenter_id }}">
                                                <?php echo getimage("images/activate_new.png"); ?></a>
                                                <?php } else {?>
                                                <a
                                                      class="activate-salescentersaleuser btn"
                                                         href="javascript:void(0)"
                                                         data-toggle="tooltip"
                                                         data-placement="top" data-container="body"
                                                         title=""
                                                         data-original-title="Activate Agent"
                                                         data-uid="{{ $agent_user->id }}"
                                                         id="delete-salescentersaleuser-{{ $agent_user->id }}"
                                                         data-salescentersaleuser="{{ $agent_user->first_name }} {{ $agent_user->last_name }}"
                                                         data-sid="{{ $agent_user->salescenter_id }}">
                                                <?php echo getimage("images/deactivate_new.png"); ?></a>
                                                <?php }?>
                                                <a
                                                      class="delete-salescentersaleuser btn"
                                                         href="javascript:void(0)"
                                                         data-toggle="tooltip"
                                                         data-placement="top" data-container="body"
                                                         title=""
                                                         data-original-title="Delete Agent"
                                                         data-uid="{{ $agent_user->id }}"
                                                         id="delete-salescentersaleuser-{{ $agent_user->id }}"
                                                         data-salescentersaleuser="{{ $agent_user->first_name }} {{ $agent_user->last_name }}"
                                                         data-sid="{{ $agent_user->salescenter_id }}">
                                                <?php echo getimage("images/cancel.png"); ?></a>

                                           <?php }?>
                                 </div>
                              </td>
                           </tr>
                           @endforeach
                           @if(count($users)==0)
                           <tr class="list-users">
                              <td colspan="5" class="text-center">No Record Found</td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
                      @if(count($users)>0)
                           <div class="btnintable bottom_btns">
                           {!! $users->appends(request()->query())->links() !!}
                           </div>
                          @endif
                        @endif
                  </div>
               </div>
        </div>
</div>

 <!-- Modal -->
 @include('client.salescenter.salesagent.salesagentspoup')
 	 <!--Modal starts-->
	  <div class="team-addnewmodal">
	  	<div class="modal fade" id="addnewsalesagent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">

			</div>
		  </div>
	</div>
 </div>
		<!--Modal starts-->

@endsection
