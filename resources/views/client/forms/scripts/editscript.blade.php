@extends('layouts.admin')
@section('content')
<?php 
   $breadcrum = array();
   if( Auth::user()->access_level =='tpv'){
     $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients' );
     $breadcrum[] = array('link' => route('client.show',$client->id) , 'text' =>  $client->name );
   }
     $breadcrum[] = array('link' =>  route('client.contact-forms',['id' => $client->id]) , 'text' =>  'Forms' );
     $breadcrum[] = array('link' => route('client.contact-page-layout',['id' => $client->id, 'form_id' => $form_id]) , 'text' =>  $form_detail->formname );
     $breadcrum[] = array('link' => route('client.contact-forms-scripts-langauge',['client_id' => $client->id, 'form_id' => $form_id]) , 'text' =>  $language  );
     $breadcrum[] = array('link' => route('client.contact-forms-scripts',['client_id' => $client->id, 'form_id' => $form_id, 'language' => $language]) , 'text' =>  'Scripts'  );
     $breadcrum[] = array('link' => '', 'text' =>  $script_detail->title );
     breadcrum ($breadcrum);
    ?>

 <div class="tpv-contbx edit-agentinfo">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">
							
						  	<div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
								<h1>Edit Script Info</h1>
                            </div>
                            <div class="tpvbtn">
                                <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                                    <a class="btn btn-green" href="{{ route('client.add-script-questions',['client_id' => $client_id, 'form_id' => $form_id,'script_id' => $script_detail->id] ) }}" >New Question<span class="add"><?php echo getimage('images/add.png') ?></span></a>
                                </div>
                                <div class="clearfix"></div>
                                @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                                @endif								
                            </div>
							
                           
						  	<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
							<!-- Nav tabs -->
							  <!-- Tab panes -->
							  <div class="tab-content">
								
							  <!--agent details starts-->
								  
								  <div class="row">
									 <div class="col-xs-12 col-sm-12 col-md-12">
										
										<div class="agent-detailform">
											<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                                
                                              
                                              
                                                <form class="form-horizontal"  role="form" method="POST"  action="">
                                                {{ csrf_field() }} 
                                                {{ method_field('POST') }}
                                                <input type="hidden" class="clientid" name="client_id" value="{{$client_id}}">
                                                <input type="hidden" class="form_id" name="form_id" value="{{$form_id}}">
												  <div class="form-group">
												    <label for="scripttitle">Title</label>
													<input class="form-control" name="title" id="scripttitle" value="{{$script_detail->title}}" type="Text" placeholder="Title">
													 <?php  echo getFormIconImage("images/title.png");?>
                                                  </div>
                                                  <div class="form-group">
                                                  <label for="scripttitle">Script</label>
                                                  <select class="form-control selectmenu" required name="scriptfor" >    
                                                        <option value="">Select</option>      
                                                        @foreach($script_for as $key => $val)                
                                                        <option 
                                                        value="{{$key}}"                                       
                                                        @if($script_detail->scriptfor == $key ) selected @endif
                                                        >{{$val}}
                                                        </option>    
                                                    @endforeach                                        
                                                    
                                                    </select>
                                                 </div>
												   
												  <div class="btnintable bottom_btns">
													<div class="btn-group">
                                                        <button class="btn btn-green" type="submit">Update<span class="add"><?php echo getimage("images/update_w.png"); ?></span></button>
                                                        <a href="{{ route('client.view-script-questions', ['client_id' => $client_id, 'form_id' =>$form_id, 'script_id' => $script_detail->id ])}}" class="btn btn-purple"> Questions<span class="browse"><?php echo getimage('images/view_w.png') ?></span></a>

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

<?php 
$added_fields = 0;
$formid = 0;
 ?>  
 
  @endsection

 
