@extends('layouts.admin')
@section('content')

<?php 
$breadcrum = array(
      array('link' => route('teammembers.index'), 'text' =>  'Team Members' ),
      array('link' => "", 'text' =>  $user->first_name ),
);
?>
{{breadcrum ($breadcrum)}} 



		<div class="tpv-contbx">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">  
						  <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
						  <div class="client-bg-white">
						  <h1>TPV Member</h1>
						  
								<div class="table-responsive mt30">
									<table class="table">
										<thead>
										  <tr class="acjin">
											<th>#ID</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th class="visi-hidden">Email</th>
											<th class="visi-hidden">Roles</th>
										  </tr>
										</thead>
										<tbody>
										  <tr>
											<td class="light_c">{{ $user->userid }}</td>
											<td class="white_c">{{ $user->first_name }}</td>
											<td class="white_c">{{ $user->last_name }}</td>
											<td class="white_c">{{ $user->email }}</td>
											<td class="light_c">@if(!empty($user->roles))
                                                @foreach($user->roles as $role)
                                                    {{ $role->display_name }} 
                                                @endforeach
                                            @endif
                                        </td>
                                            </tr> 
         
										</tbody>
									  </table>
									
									<div class="btnintable bottom_btns">
										<div class="btn-group">
                                        <?php if(Auth::user()->can(['user-update'])){
                                            edit_btn_on_view_info_page( route('teammembers.edit',$user->id))
                                             ?>
                                       <?php } ?>
                                       <?php if(Auth::user()->can(['user-delete'])){
                                           delete_btn_on_view_info_page("javascript:void(0)", "deleteuser", 'data-uid="'.$user->id.'" id="delete-user-'.$user->id.'" data-teamuser="'. $user->first_name .' '. $user->last_name .'" ' );
                                              } ?>
										 
										</div>
									</div>
									
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
    </div> 



 @include('teammembers.deleteteamuserpoup')  

@endsection