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
$breadcrum[] =  array('link' => '' , 'text' =>  $template->name); 
breadcrum ($breadcrum);
 ?>
 	<div class="tpv-contbx edit-agentinfo">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">
							
						  	<div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
								<h1>Edit Template</h1>
							</div>
							
						  	<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
							<!-- Nav tabs -->
							  <!-- Tab panes -->
							  <div class="tab-content">
								
							  <!--agent details starts-->
								  
								  <div class="row">
									 <div class="col-xs-12 col-sm-12 col-md-12">
										
										<div class="agent-detailform">
											<div class="col-xs-12 ">
                       @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <form  role="form" method="POST"  action="">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                            <input type="hidden" class="clientid" name="client_id" value="{{$client_id}}">
												   <div class="form-group">
												    <label for="exampleInputName2"></label>
                            <input class="form-control" required name="name" id="templatename" value="{{$template->name}}" type="Text" placeholder="Name">
												 
												   </div>
												   <div class="dropdown agent-edit">
                             <select class="form-control selectsearch" id="selectform4mapping" required name="form_id" >
                                    <option value="">Select</option>
                                    @foreach($forms as   $form)
                                    <option value="{{$form->id}}" @if($template->form_id == $form->id ) selected @endif>{{$form->formname}}</option>
                                    @endforeach
                                </select>

                                <span class="invalid-feedback validation-error text-danger">
                                        <strong></strong>
                                    </span>
												   </div>
													
													
												  <div class="col-xs-12 col-sm-12 col-md-12">
													<p class="compliance-heading">Add New</p>
												  </div>	
												 
												  <div class="row">
													<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx compliance_edit">
													<div class="table-responsive">
														<table class="table">
															<thead>
															  <tr class="acjin">
																<th>Header Name</th>
																<th>Value</th>
																<th style="width:40px">Custom</th>
																<th style="width:40px"></th>
															  </tr>
															</thead>
															<tbody>
															  <tr>
																<td class="light_c header-name">
																  <input type="text" class="form-control inline-block" id="addnewoption" placeholder="Header Name"  style="padding: 4px 12px;" >
																</td>
																<td class="white_c">
																	<div class="dropdown">
																    	<select class="form-control selectsearch select_option_for_compliance" id="newoptiontoadd"  >
                                               <option value="">Select</option>
                                               <?php echo $fields_option ?>
                                            </select>
                                        <input type="text" class="form-control get_custom_value_for" value="" style='display:none;padding:4px 12px;'>
																   </div>
																</td>
																<td class="white_c">
																  <div class="btn-group">
																	<label class="checkbx-style">
                                       <input type="checkbox" class="checktoallow_custom" name="" value=""  style="margin:0 auto;">
																		  <span class="checkmark"></span>
																	</label>  
																
																  </div>
                                </td>
                                <td class="light_c">	<a class="btn add_compliance_option" href="javascript:void(0);" role="button"><img src="/images/add_green.png"/></a></td>
															  </tr>
															</tbody>
														  </table>

												  <div class="content4maping form-group">
                              <ul class="compliance_options_selector dd-list">
                                      <li class="dd-item ui-state-disabled">
                                             <div class="dd-handle">
                                                <div class="compliance-first-div question-heading">Header</div>
                                                <div class="compliance-second-div question-heading">Values</div>
                                                <div class="compliance-third-div question-heading">  </div>
                                             </div>
                                          </li>
                               </ul>
                                <?php
                                 $header_options = unserialize($template->fields);
                            ?>
                                 <ul class="dd-list compliance_options_selector" id="optionssortabletable">
                                @if(count($header_options['header']) > 0)
                                 <?php $i = 0;?>
                                  @foreach ($header_options['header'] as   $headeroption )
                                   @if(!empty($headeroption))
                                   <li class="dd-item options_row options_row_{{$i}}">
                                       <div class="dd-handle">
                                          <div  class="valign-middle compliance-first-div"> {{$headeroption}}
                                            <input type="hidden" value="{{$headeroption}}" name="header_column[header][]">
                                          </div>
                                          <div class="valign-middle compliance-second-div">
                                            @if(isset($header_options['allow_custom'][$i]) && $header_options['allow_custom'][$i]== 1 )
                                            {{$header_options['custom_value'][$i]}}
                                            @else
                                              {{$header_options['values'][$i]}}
                                            @endif

                                            <input type="hidden" value="{{$header_options['values'][$i]}}" name="header_column[values][]">
                                            @if(isset($header_options['allow_custom'][$i]))
                                              <input type="hidden" value="{{$header_options['allow_custom'][$i]}}" name="header_column[allow_custom][]">
                                            @else
                                              <input type="hidden" value="" name="header_column[allow_custom][]">
                                            @endif

                                            @if(isset($header_options['custom_value'][$i]))
                                            <input type="hidden" value="{{$header_options['custom_value'][$i]}}" name="header_column[custom_value][]">
                                            @else
                                              <input type="hidden" value="" name="header_column[custom_value][]">
                                            @endif


                                          </div>
                                            <div class="valign-middle compliance-third-div"><a href="javascript:void(0);" class="remove_compliance_option" data-rel="options_row_{{$i}}"><?php echo getimage('images/cancel.png'); ?></a>
                                            </div>
                                       </div>
                                   </li>

                                      <?php $i++;?>
                                      @endif
                                  @endforeach
                                @endif
                              </ul>
                            </div>
                            <div class="clearfix"></div>

													</div>
												</div>
												  </div> 
												  
												  <div class="btnintable bottom_btns">
													<div class="btn-group">
														<button class="btn btn-green" type="submit">Update<span class="add"><img src="/images/update_w.png"/></span></button>
													 
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
<script>
  window.mapwithform = "{{ route('client.compliance-mapoptions',$client_id)}}";
 </script>

  @endsection
