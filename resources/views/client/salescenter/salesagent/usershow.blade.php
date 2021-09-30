@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(  );
if(Auth::user()->access_level == 'tpv' || Auth::user()->access_level == 'client' ){
    $breadcrum[] = array('link' =>  route('client.findsalesagents',['client' => $client_id,'salecenter' => $salescenter_id, 'location' => $location->id ]), 'text' =>  'Find Sales Agent' );

}else{
    $breadcrum[] = array('link' => route('client.salescenter.salesagents',['client_id' => $client_id,'salescenter_id' => $salescenter_id    ]), 'text' =>  'Sales Agents' );
}
$breadcrum[] = array('link' => '', 'text' =>   $user->first_name);
breadcrum ($breadcrum);
?>

 <div class="tpv-contbx">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">

						  	<div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
								<h1>Agent Info</h1>
							</div>

                                @if ($message = Session::get('success'))
                                   <div class="tpvbtn">
                                     <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="alert alert-success">
                                            <p>{{ $message }}</p>
                                        </div>
                                        </div>
                                   </div>
                                @endif


						  <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
								<div class="table-responsive">

									<table class="table">
										<thead>
										  <tr class="heading">
                                            <th>ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>Location</th>
										  </tr>
										</thead>
										<tbody>
										  <tr>
                                          <td class="light_c">{{ $user->userid }}</td>
                                          <td class="light_c">{{ $user->first_name }}</td>
                                          <td class="light_c">{{ $user->last_name }}</td>
                                          <td class="light_c">{{ $user->email }}</td>
                                          <td class="light_c">{{ $location->name }} (
                                                      {{ $location->street }} {{ $location->city }}, {{ $location->state }}, {{ $location->country }}, {{ $location->zip }}  )
                                          </td>
										 </tr>
										</tbody>
									  </table>

									<div class="btnintable bottom_btns">
										<div class="btn-group">

                                            <a class="btn btn-green"
                                             href="{{ route('client.salescenter.salesagent.edit',['client_id' => $user->client_id, 'salescenter_id'=>$user->salescenter_id, 'userid' =>$user->id,'client'=>$user->client_id,  'salecenter'=>$user->salecenter_id,'location' => $user->location_id ,'ref'=>'findagent']) }}"
                                            >Edit</a>

                                            <?php if($user->status=='active'){ ?>
                                                <a class="deactivate-salescentersaleuser btn btn-red"
                                                    href="javascript:void(0)"
                                                    data-toggle="tooltip"
                                                    data-placement="top" data-container="body"
                                                    title=""
                                                    data-original-title="Deactivate Agent"
                                                    data-uid="{{ $user->id }}"
                                                    id="delete-salescentersaleuser-{{ $user->id }}"
                                                    data-salescentersaleuser="{{ $user->first_name }} {{ $user->last_name }}"
                                                    data-sid="{{ $user->salescenter_id }}" >Deactivate</a>
                                                <?php } else {?>
                                                <a  class="activate-salescentersaleuser btn btn-green"
                                                    href="javascript:void(0)"
                                                    data-toggle="tooltip"
                                                    data-placement="top" data-container="body"
                                                    title=""
                                                    data-original-title="Activate Agent"
                                                    data-uid="{{ $user->id }}"
                                                    id="delete-salescentersaleuser-{{ $user->id }}"
                                                    data-salescentersaleuser="{{ $user->first_name }} {{ $user->last_name }}"
                                                    data-sid="{{ $user->salescenter_id }}">Activate </a>
                                                <?php }?>



										</div>
									</div>

								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
</div>

@include('client.salescenter.salesagent.salesagentspoup')
@endsection
