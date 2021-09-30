@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if( Auth::user()->access_level =='tpv')
{
  $breadcrum[] =  array('link' => route('utilities.index') , 'text' =>   'Utilities');
  $breadcrum[] =  array('link' => route('utilities.index',['client' => $client->id]) , 'text' =>  $client->name);
}
$breadcrum[] =  array('link' => route('utilities.index',['client' => $client->id,'search_text' => $utility->utilityname]) , 'text' =>  $utility->utilityname);
$breadcrum[] =  array('link' => '' , 'text' =>  'Compliance Templates');
breadcrum ($breadcrum);
 ?>

	<div class="tpv-contbx">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">
						  	<div class="tpvbtn">
								<div class="col-xs-12 col-sm-12 col-md-12 top_sales">
									<a class="btn btn-green" href="{{ route('utility.compliance-add-templates',['client_id' => $client->id, 'utility_id' => $utility->id]) }}"   data-toggle="modal" data-target="#addnew_template">New Template<span class="add"><img src="/images/add.png"/></span></a>
								</div>

							</div>
              @if ($message = Session::get('success'))
                 <div class="tpvbtn">
							    	<div class="col-xs-12 col-sm-12 col-md-12 top_sales">
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
										  <tr class="acjin">
											<th>Sr.No</th>
											<th>Name</th>
											<th>Form</th>
										    <th>Actions</th>
										  </tr>
										</thead>
										<tbody>
                    @if(count($templates) > 0)
                     <?php $i = 0; ?>
                            @foreach ($templates as $key => $template)
                            <?php if($i % 2 == 0){
                                     $first_last_td_class = "dark_c";
                                     $second_and_middle_td_class = "grey_c";
                                }else{
                                     $first_last_td_class = "light_c";
                                       $second_and_middle_td_class = "white_c";
                                }
                                ?>
                            <tr class="list-users">
                                    <td class="{{ $first_last_td_class }}">{{ ++$i }}</td>
                                    <td class="{{ $second_and_middle_td_class }}">{{ $template->name }}</td>
                                    <td class="{{ $second_and_middle_td_class }}">{{ $template->formname }}</td>
                                    <td class="{{ $first_last_td_class }}">
                                       <div class="btn-group">
                                           <a class="btn" href="{{  route('utility.compliance-edit-template',['client_id' => $client->id, 'utility_id' => $utility->id, 'id' => $template->id]) }}" role="button" data-toggle="tooltip"
                                        data-placement="top" data-container="body"
                                        title=""
                                        data-original-title="Edit/View Template" ><img src="/images/edit.png"/></a>
                                           <a class="btn delete-compliance-template" href="javascript:void(0)"
                                          data-toggle="tooltip"
                                          data-placement="top" data-container="body"
                                          title=""
                                          data-original-title="Delete Template"
                                          data-id="{{ $template->id }}"
                                          data-cid="{{ $client_id }}"
                                          id="delete-form-{{ $template->id }}"
                                          data-formname="{{ $template->name }}"
                                          role="button"><img src="/images/cancel.png"/></a>
                                      </div>
                                  </td>
                                </tr>
                            @endforeach
                            @endif
                            @if( count($templates) == 0)
                             <tr>
                               <td colspan="4" class="text-center"> <h2>No Record Found</h2>  </td>
                             </tr>
                            @endif

									    </tbody>
									  </table>
                    {!! $templates->render() !!}
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>

  @include('client.compliance.poup')
  <div class="team-addnewmodal">
	  	<div class="modal fade" id="addnew_template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
      </div>
		  </div>
	</div>
 </div>
@endsection
