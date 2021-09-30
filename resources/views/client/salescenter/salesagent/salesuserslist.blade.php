@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' =>'', 'text' =>  'Sales Agents');

breadcrum ($breadcrum);
 ?>

 <div class="tpv-contbx">
			<div class="container">
					<div class="col-xs-12 col-sm-12 col-md-8">
                      @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                     </div>
                     <div class="row">
                     <div class="col-xs-12 col-sm-12 col-md-12 top_sales">

                                <a class="btn btn-green" href="{{ route('client.salescenter.addsalesagent',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id  ]) }}"  data-toggle="modal" data-target="#addnewsalesagent">Add New Sales Agent<span class="add"><?php echo getimage('images/add.png') ?></span></a>

                             </div>


                   <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                        <div class="cont_bx3">
                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr class="acjin">
                                                    <th>Sr. No.</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th class="visi-hidden" style="width:200px;">Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $j = 0;?>
                                                @foreach ($agent_users as $key => $agent_user)
                                                <?php
                                                    $j++;
                                                    if($j % 2 == 0){
                                                        $first_last_td_class = "light_c";
                                                        $second_and_middle_td_class = "white_c";
                                                    }else{
                                                        $first_last_td_class = "dark_c";
                                                        $second_and_middle_td_class = "grey_c";
                                                        }
                                                    ?>
                                                    <tr >
                                                        <td class="{{$first_last_td_class }}">{{ ++$i }}</td>
                                                        <td class="{{$second_and_middle_td_class }}"><a href="{{ route('client.salescenter.salesagent.show',['client_id' => $client_id, 'salescenter_id'=>$agent_user->salescenter_id, 'userid' =>$agent_user->id  ]) }}" >{{ $agent_user->first_name }} {{ $agent_user->last_name }}</a></td>
                                                        <td class="{{$second_and_middle_td_class }}">{{ $agent_user->email }}</td>
                                                        <td class="{{$first_last_td_class }}">
                                                        <div class="btn-group">
                                                        <a class="btn"
                                                        href="{{ route('client.salescenter.salesagent.show',['client_id' => $client_id, 'salescenter_id'=>$agent_user->salescenter_id, 'userid' =>$agent_user->id  ]) }}"
                                                        data-toggle="tooltip"
                                                        data-placement="top" data-container="body"
                                                        title=""
                                                        data-original-title="View Agent"
                                                        role="button"><?php echo getimage("images/view.png"); ?></a>

                                                        <a class="btn"
                                                        href="{{ route('client.salescenter.salesagent.edit',['client_id' => $client_id, 'salescenter_id'=>$agent_user->salescenter_id, 'userid' =>$agent_user->id,'client'=>$client_id,  'salecenter'=>$salecenter_id,'location' => $agent_user->location_id ,'ref'=>'findagent']) }}"
                                                        data-toggle="tooltip"
                                                        data-placement="top" data-container="body" title=""
                                                        data-original-title="Edit Agent"
                                                        role="button"><?php echo getimage("images/edit.png"); ?></a>


                                                         <?php if($agent_user->status=='active'){ ?>

                                                                <a
                                                                class="deactivate-salescentersaleuser btn"
                                                                href="javascript:void(0)"
                                                                data-toggle="tooltip"
                                                                data-placement="top" data-container="body"
                                                                title=""
                                                                data-original-title="Deactivate Agent"
                                                                data-uid="{{ $agent_user->id }}"
                                                                id="delete-salescentersaleuser-{{ $agent_user->id }}"
                                                                data-salescentersaleuser="{{ $agent_user->first_name }} {{ $agent_user->last_name }}" data-sid="{{ $agent_user->salescenter_id }}" >
                                                                <?php echo getimage("images/activate_new.png"); ?>
                                                                </a>

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
                                                                <?php echo getimage("images/deactivate_new.png"); ?>
                                                                </a>

                                                                <?php }?>


                                                    </div>


                                                    </td>
                                                    </tr>
                                                    @endforeach
                                                    @if(count($agent_users)==0)
                                                    <tr class="list-users">
                                                        <td colspan="4" class="text-center">No Record Found</td>
                                                    </tr>
                                                    @endif

                                                </tbody>
                                            </table>


                                                @if(count($agent_users)>0)
                                                <div class="btnintable bottom_btns">
                                                {!! $agent_users->render() !!}
                                                </div>
                                                @endif


                                        </div>

                         </div>
                     </div>
                  </div>
             </div>
   </div>

@include('client.salescenter.salesagent.salesagentspoup')
<div class="team-addnewmodal">
	  	<div class="modal fade" id="addnewsalesagent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">

			</div>
		  </div>
	</div>
 </div>

@endsection
