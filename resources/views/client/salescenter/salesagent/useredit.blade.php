@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(  );
if(Auth::user()->access_level == 'tpv' || Auth::user()->access_level == 'client' ){
    $breadcrum[] = array('link' =>   route('client.findsalesagents',['client' => $client_id,'salecenter' => $salescenter_id, 'location' => $user->location_id ]), 'text' =>  'Find Sales Agent' );

}else{
    $breadcrum[] = array('link' => route('client.salescenter.salesagents',['client_id' => $client_id,'salescenter_id' => $salescenter_id    ]), 'text' =>  'Sales Agents' );
}
$breadcrum[] = array('link' => '', 'text' =>   $user->first_name);
breadcrum ($breadcrum);
?>


	<div class="tpv-contbx edit-agentinfo">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">

						  	<div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
								<h1>Edit Agent Info</h1>
							</div>

						  	<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">

							<!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#agentdetail" aria-controls="home" role="tab" data-toggle="tab">Profile information</a></li>
                                <li role="presentation"><a href="#otherdetails" aria-controls="profile" role="tab" data-toggle="tab">Other details</a></li>

							  </ul>


							  <!-- Tab panes -->
							  <div class="tab-content">

							  <!--agent details starts-->
                              <div role="tabpanel" class="tab-pane active" id="agentdetail">
								  <div class="row">
									 <div class="col-xs-12 col-sm-12 col-md-12">

										<div class="agent-detailform">
											<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                                  <!-- Display Validation Errors -->
                                            @if ($message = Session::get('success'))
                                                <div class="alert alert-success">
                                                    <p>{{ $message }}</p>
                                                </div>
                                            @endif
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
                                            <form  role="form"  enctype="multipart/form-data"    method="POST" action="{{ route('client.salescenter.salesagent.edit',['company_id' => $user->client_id,'salescenter_id'=>$user->salescenter_id, 'userid' => $user->id]) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('post') }}
                                                    @if(count($reference_array) > 0)
                                                        <?php
                                                        foreach($reference_array as  $reference_fieldname => $field_value): ?>
                                                        <input type="hidden" name="{{$reference_fieldname}}" value="{{$field_value}}">
                                                        <?php endforeach ?>
                                                    @endif

												  <div class="form-group {{  $errors->has('first_name')  ? ' has-error' : '' }}">
												    <label for="first_name"></label>
													<input id="first_name" autocomplete="off" type="text" class="form-control" name="first_name"   required placeholder="First Name"  value="{{$user->first_name}}" >
                                                    <?php echo getFormIconImage("images/form-name.png"); ?>
                                                    @if ($errors->has('first_name'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                       </span>
                                                    @endif
												  </div>
												  <div class="form-group {{  $errors->has('last_name')  ? ' has-error' : '' }}">
												    <label for="last_name"></label>
													<input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name"  placeholder="Last Name" value="{{$user->last_name}}" >
                                                    <?php echo getFormIconImage("images/form-name.png"); ?>
                                                    @if ($errors->has('last_name'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('last_name') }}</strong>
                                                    </span>
                                                    @endif
												  </div>
												  <div class="form-group {{  $errors->has('email')  ? ' has-error' : '' }}">
												     <label for="email"></label>
                                                     <input id="email" type="email" class="form-control" name="email" value="{{$user->email}}" required placeholder="E-Mail"  >
                                                     <?php echo getFormIconImage("images/form-email.png"); ?>
                                                        @if ($errors->has('email'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                        @endif
                                                  </div>

												  <div class="form-group {{  $errors->has('password')  ? ' has-error' : '' }}">
                                                    <label for="password"></label>
                                                    <input id="password" type="password" class="form-control" name="password"  placeholder="Password">
                                                    <?php echo getFormIconImage("images/form-pass.png"); ?>
                                                        @if ($errors->has('password'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                        @endif

												  </div>
												  <div class="form-group {{  $errors->has('password_confirmation')  ? ' has-error' : '' }}">
                                                    <label for="password_confirmation"></label>
                                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"  placeholder="Confirm Password" >
                                                    <?php echo getFormIconImage("images/form-pass.png"); ?>
                                                        @if ($errors->has('password_confirmation'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                        </span>
                                                        @endif
												  </div>
												  <div class="row">
													<div class="col-xs-12 col-sm-6 col-md-6">
													     <div class="dropdown agent-edit {{  $errors->has('location')  ? ' has-error' : '' }}">
                                                          <select name="location" class="form-control selectmenu" required >
                                                            <option value=""> Select </option>
                                                            @foreach($locations as $location)
                                                            <option value="{{$location->id}}" <?php if($location->id==$user->location_id) echo "selected='selected'"; ?>> {{$location->name}} </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('location'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('location') }}</strong>
                                                            </span>
                                                            @endif

														 </div>
												  	</div>
													  <div class="col-xs-12 col-sm-6 col-md-6">
														<div class="dropdown agent-edit  {{  $errors->has('formid')  ? ' has-error' : '' }}">
                                                           <select name="formid" class="selectmenu" id="" required >
                                                             <option value="">Select</option>
                                                             @if(count($clientsforms) > 0)
                                                                @foreach($clientsforms as $form)
                                                                <option value="{{ $form->id }}"
                                                                @if($assignedform == $form->id) selected @endif
                                                                >{{ $form->formname }}</option>
                                                                @endforeach
                                                            @endif


                                                            </select>
                                                            @if ($errors->has('formid'))
                                                                <span class="help-block">
                                                                 <strong>{{ $errors->first('formid') }}</strong>
                                                                </span>
                                                            @endif
														 </div>
													  </div>


												  </div>
                                                  <div class="form-group {{  $errors->has('email')  ? ' has-error' : '' }}" style="margin-top:25px;">
												     <label for="agentdoc"> Upload file </label>
                                                          <input id="agentdoc" class="file2 inline btn btn-purple" data-label='Browse<span class="browse"><?php  echo getimage("images/browse_w.png"); ?></span>' name="agentdoc"  type="file">
                                                                @if ($errors->has('agentdoc'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('agentdoc') }}</strong>
                                                                </span>
                                                                @endif
                                                  </div>

												  <div class="btnintable bottom_btns">
													<div class="btn-group">
														<button class="btn btn-green" type="submit">Update<span class="add"><?php echo getimage("images/update_w.png"); ?></span></button>
														<a class="btn btn-red"  href="{{$backurl}}">Cancel<span class="del"> <?php echo getimage("images/cancel_w.png"); ?></span></a>
													</div>
												  </div>
												</form>


											</div>
										</div>


									  </div>
									</div>
									</div>
                                    <!-- Other details -->
                                    <div role="tabpanel" class="tab-pane" id="otherdetails">
                                       <div class="row">
									      <div class="col-xs-12 col-sm-12 col-md-12">
										     <div class="agent-detailform">
                                                 <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                                        <form  role="form"  enctype="multipart/form-data"    method="POST" action="{{ route('client.salescenter.salesagent.editdetail',['company_id' => $user->client_id,'salescenter_id'=>$user->salescenter_id, 'userid' => $user->id]) }}">
                                                                {{ csrf_field() }}
                                                                {{ method_field('post') }}
                                                                @if(count($reference_array) > 0)
                                                                    <?php
                                                                    foreach($reference_array as  $reference_fieldname => $field_value): ?>
                                                                    <input type="hidden" name="{{$reference_fieldname}}" value="{{$field_value}}">
                                                                    <?php endforeach ?>
                                                                @endif

                                                                <div class="form-group radio-btns text-left  flex {{  $errors->has('passed_state_test')  ? ' has-error' : '' }}">
                                                                    <label for="passed_state_test">Passed state test</label>
                                                                    <div class="clearfix"></div>
                                                                    <label class="radio-inline">
                                                                    <input type="radio"  id="passedtestyes"  name="passed_state_test" value="1"  @if( isset($user_details['passed_state_test']) && $user_details['passed_state_test']=='1' ) checked @endif >Yes
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                    <input type="radio" id="passedtestno" name="passed_state_test" value="0" @if( isset($user_details['passed_state_test']) && $user_details['passed_state_test']=='0' || !isset($user_details['passed_state_test'])  ) checked @endif  >No
                                                                    </label>
                                                                    @if ($errors->has('passed_state_test'))
                                                                        <span class="help-block">
                                                                        <strong>{{ $errors->first('passed_state_test') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>

                                                                <div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
                                                                <label for="state">State</label>
                                                                    <div class="dropdown select-dropdown">
                                                                        <select class="selectsearch form-control" id="state" name="state">
                                                                            <option value="">Select</option>
                                                                            <option value="MA"  @if( isset($user_details['state']) && $user_details['state']=='MA' ) selected @endif   >MA</option>
                                                                            <option value="MD"  @if( isset($user_details['state']) && $user_details['state']=='MD' ) selected @endif  >MD</option>
                                                                            <option value="NJ"  @if( isset($user_details['state']) && $user_details['state']=='NJ' ) selected @endif  >NJ</option>
                                                                            <option value="NY"  @if( isset($user_details['state']) && $user_details['state']=='NY' ) selected @endif  >NY</option>
                                                                            <option value="OH"  @if( isset($user_details['state']) && $user_details['state']=='OH' ) selected @endif  >OH</option>
                                                                            <option value="PA"  @if( isset($user_details['state']) && $user_details['state']=='PA' ) selected @endif  >PA</option>
                                                                            <option value="IL"  @if( isset($user_details['state']) && $user_details['state']=='IL' ) selected @endif  >IL</option>
                                                                            <option value="CT"  @if( isset($user_details['state']) && $user_details['state']=='CT' ) selected @endif  >CT</option>
                                                                            <option value="TX"  @if( isset($user_details['state']) && $user_details['state']=='TX' ) selected @endif  >TX</option>
                                                                            <option value="MI"  @if( isset($user_details['state']) && $user_details['state']=='MI' ) selected @endif  >MI</option>
                                                                        </select>
                                                                        @if ($errors->has('state'))
                                                                            <span class="help-block text-danger">
                                                                            <strong>{{ $errors->first('state') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                </div>
                                                            </div>
                                                            <div class="form-group text-left radio-btns flex {{  $errors->has('certified')  ? ' has-error' : '' }}">
                                                                    <label for="cerifiedtest">Certified</label>
                                                                    <div class="clearfix"></div>
                                                                    <label class="radio-inline">
                                                                    <input type="radio"  id="certifiedyes"  name="certified" value="1" @if( isset($user_details['certified']) && $user_details['certified']=='1'  ) checked @endif >Yes
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                    <input type="radio" id="certifiedno" name="certified" value="0"  @if( isset($user_details['certified']) && $user_details['certified']=='0' || !isset($user_details['certified'])  ) checked @endif >No
                                                                    </label>
                                                                    @if ($errors->has('certified'))
                                                                        <span class="help-block">
                                                                        <strong>{{ $errors->first('certified') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                                <div class="form-group {{ $errors->has('codeofconduct') ? ' has-error' : '' }}">
                                                                    <label for="codeofconduct">Code of conduct</label>
                                                                    <textarea name="codeofconduct" id="codeofconduct"  class="form-control" cols="30" rows="5" placeholder="Code of conduct" style="padding:5px">@if( isset($user_details['codeofconduct'])   ) {{$user_details['codeofconduct']}} @endif</textarea>
                                                                    @if ($errors->has('codeofconduct'))
                                                                        <span class="help-block">
                                                                        <strong>{{ $errors->first('codeofconduct') }}</strong>
                                                                    </span>
                                                                    @endif
                                                              </div>
                                                              <div class="form-group text-left radio-btns flex {{  $errors->has('backgroundcheck')  ? ' has-error' : '' }}">
                                                                    <label for="backgroundcheck">Background Check</label>
                                                                    <div class="clearfix"></div>
                                                                    <label class="radio-inline">
                                                                    <input type="radio"  id="backgroundcheckyes"  name="backgroundcheck" value="1" @if( isset($user_details['backgroundcheck']) && $user_details['backgroundcheck']=='1'  ) checked @endif  >Yes
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                    <input type="radio" id="backgroundcheckno" name="backgroundcheck" value="0" @if( isset($user_details['backgroundcheck']) && $user_details['backgroundcheck']=='0' || !isset($user_details['backgroundcheck'])  ) checked @endif  >No
                                                                    </label>
                                                                    @if ($errors->has('backgroundcheck'))
                                                                        <span class="help-block">
                                                                        <strong>{{ $errors->first('backgroundcheck') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>

                                                                <div class="form-group text-left radio-btns flex {{  $errors->has('drugtest')  ? ' has-error' : '' }}">
                                                                    <label for="drugtest">Drug Test</label>
                                                                    <div class="clearfix"></div>
                                                                    <label class="radio-inline">
                                                                    <input type="radio"  id="drugtestyes"  name="drugtest" value="1"  @if( isset($user_details['drugtest']) && $user_details['drugtest']=='1'   ) checked @endif >Yes
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                    <input type="radio" id="drugtestno" name="drugtest" value="0"  @if( isset($user_details['drugtest']) && $user_details['drugtest']=='0' || !isset($user_details['drugtest'])  ) checked @endif >No
                                                                    </label>
                                                                    @if ($errors->has('drugtest'))
                                                                        <span class="help-block">
                                                                        <strong>{{ $errors->first('drugtest') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                                <div class="form-group {{ $errors->has('certification_date') ? ' has-error' : '' }}">
                                                                    <label for="certification_date">Certification Date</label>
                                                                    <input id="certification_date" autocomplete="off"  type="text" class="form-control singledate" name="certification_date" value="@if( isset($user_details['certification_date']) && $user_details['certification_date']!=''){{ date('m/d/Y',strtotime($user_details['certification_date']))}} @endif" placeholder="Certification Date" >


                                                                            @if ($errors->has('certification_date'))
                                                                                <span class="help-block">
                                                                                <strong>{{ $errors->first('certification_date') }}</strong>
                                                                            </span>
                                                                            @endif
                                                                </div>



                                                                <div class="btnintable bottom_btns">

                                                                        <div class="btn-group">

                                                                            <button class="btn btn-green" type="submit">Submit<span class="add"><img src="/images/save.png"/></span></button>

                                                                        </div>
                                                                </div>




                                                        </form>
                                                    </div>
                                             </div>
                                           </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
                                    <br/>
                                    <br/>
							         	<h1>Documents</h1>
							       </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx" >


                                          <div class="table-responsive" style="border-radius: 16px;">
                                                  <table class="table">
                                                      <thead>
                                                      <tr class="acjin">
                                                          <th>Sr. No.</th>
                                                          <th>File Name</th>

                                                          <th class="visi-hidden" style="width:120px; text-align:center">Actions</th>
                                                      </tr>
                                                      </thead>
                                                      <tbody>
                                                      @if( count( $documents) > 0)
                                                      @php
                                                        $i = 1;
                                                        @endphp
                                                         @foreach($documents as $document)
                                                            <tr>
                                                              <td>{{ $i }}</td>
                                                              <td>{{ $document->name }}</td>
                                                              <td>
                                                              <a class="btn"
                                                                    href="{{   asset('storage/'.$document->path) }}"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top" data-container="body"
                                                                    title=""

                                                                    data-original-title="View File"
                                                                    target="_blank"
                                                                    role="button"><?php echo getimage("images/view.png"); ?></a>

                                                                    <a class="btn delete-file"
                                                                    href="javascript:void(0);"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top" data-container="body"
                                                                    title=""
                                                                    data-original-title="Delete File"
                                                                    data-fid="{{ $document->id}}"
                                                                    role="button"><?php echo getimage("images/cancel.png"); ?></a>

                                                              </td>
                                                            </tr>
                                                            @php  $i++; @endphp
                                                         @endforeach
                                                      @endif
                                                      @if( count( $documents) == 0)
                                                        <tr>
                                                        <td class="text-center" colspan="3">No Record Found </td>
                                                        </tr>
                                                      @endif
                                              </tbody>
                                          </table>


                                              @if(count($documents)>0)
                                              <div class="btnintable bottom_btns">
                                              {!! $documents->render() !!}
                                              </div>
                                              @endif
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

	  	<div class="modal fade confirmation-model" id="deletefile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
                <form action="{{ route('client.salescenter.salesagent.deletefile',['company_id' => $user->client_id,'salescenter_id'=>$user->salescenter_id, 'userid' => $user->id]) }}" method="POST"  >
                <input type="hidden" value="" name="fileid" id="fileid" >
                     {{ csrf_field() }}
                     {{ method_field('POST') }}

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">File Action</h4>
                    </div>

                    <div class="modal-body">
                    Are you sure you want to delete this file.
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirm</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                    </form>

			</div>
		  </div>
	</div>

 <script>
 $('body').on('click','.delete-file',function(e){
           $('#deletefile').modal();
           var uid = $(this).data('fid');
           $('#deletefile  #fileid').val(uid);


   });
 </script>

@endsection
