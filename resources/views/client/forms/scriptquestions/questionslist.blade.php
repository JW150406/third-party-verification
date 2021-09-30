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
     $breadcrum[] = array('link' => route('client.edit-forms-script',['client_id' => $client->id, 'form_id' => $form_id, 'script_id' => $script_id]) , 'text' =>  $script_detail->title );
     $breadcrum[] = array('link' => '', 'text' =>  'Script Question' );
     breadcrum ($breadcrum);


     $scriptfor = $script_detail->scriptfor;
    ?>

<div class="tpv-contbx scripts-ques">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <h1>Script Questions</h1>
               </div>
               <div class="tpvbtn">

                  <div class="clearfix"></div>
                  @if ($message = Session::get('success'))
                  <div class="alert alert-success">
                     <p>{{ $message }}</p>
                  </div>
                  @endif
               </div>
               @if( $scriptfor =='salesagentintro')
                        <div class="col-xs-12 col-sm-12 col-md-12 ">
                              <a class="btn btn-green" href="{{ route('client.add-script-questions',['client_id' => $client->id, 'form_id' => $form_id,'script_id' => $script_id,'state' => $state,'commodity' => $commodity ] ) }}"   >New Script<span class="add"><?php echo getimage('images/add.png') ?></span></a>
                        </div>
                                                @else
                                                   <div class="col-xs-12 col-sm-12 col-md-12 ">
                                                   <div class="cont_bx3 salescenter_contbx">
                                                   <h1>Select state</h1>

                                                   <form  enctype="multipart/form-data" role="form" method="GET" action="">
                                                         <div class="row">
                                                            <div class="col-xs-12 col-sm-3 col-md-3">
                                                                  <p>Select State</p>
                                                                  <div class="dropdown {{ $errors->has('name') ? ' has-error' : '' }}">
                                                                        <select class="selectsearch select-box-admin" name="state" id="state" >
                                                                              <option value="">Select</option>
                                                                              @if(count($states)>0)
                                                                              @foreach($states as $state_data)
                                                                                 <option value="{{$state_data->state}}"  <?php if($state_data->state == $state) echo "selected='selected'"; ?>>{{$state_data->state}}</option>
                                                                              @endforeach
                                                                              @endif
                                                                        </select>
                                                                        @if ($errors->has('name'))
                                                                              <span class="help-block">
                                                                              <strong>{{ $errors->first('name') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                  </div>
                                                            </div>
                                                            @if($form_detail->commodity_type == 'DualFuel')
                                                            <input type="hidden" name="commodity" value="Dual Fuel">
                                                            @else
                                                            <div class="col-xs-12 col-sm-3 col-md-3">
                                                                  <p>Select Commodity</p>
                                                                  <div class="dropdown {{ $errors->has('state') ? ' has-error' : '' }}">
                                                                        <select class="selectsearch select-box-admin" name="commodity" id="commodity" >
                                                                           <option value="">Select</option>
                                                                           <option value="Electric"  <?php if('Electric' == $commodity) echo "selected='selected'"; ?>>Electric</option>
                                                                           <option value="Gas"  <?php if('Gas' == $commodity) echo "selected='selected'"; ?>>Gas</option>

                                                                        </select>
                                                                        @if ($errors->has('commodity'))
                                                                              <span class="help-block">
                                                                              <strong>{{ $errors->first('commodity') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                  </div>
                                                            </div>
                                                            @endif

                                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                                  <button class="btn btn-green" type="submit">Submit<span class="add"><img src="/images/update_w.png"/></span></button>
                                                               @if($state != "" && $commodity !='' && $scriptfor !='salesagentintro')
                                                                  <a class="btn btn-green" href="{{ route('client.add-script-questions',['client_id' => $client->id, 'form_id' => $form_id,'script_id' => $script_id,'state' => $state,'commodity' => $commodity ] ) }}"   >New Script<span class="add"><?php echo getimage('images/add.png') ?></span></a>
                                                                  <a
                                                               class="clone-script  select_clone2"
                                                               href="javascript:void(0);"
                                                               data-toggle="tooltip"
                                                               data-placement="top" data-container="body"
                                                               title=""
                                                               data-id=""
                                                               id="clone-question"
                                                               data-original-title="Clone all question to another state "
                                                               >
                                                               <i class="fa fa-clone select_icon" aria-hidden="true"></i>
                                                            </a>
                                                               @endif




                                                            </div>
                                                         </div>
                                                   </form>

                                             </div>
                                    </div>
                @endif
               <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                  <div class="table-responsive">

 <ul class="dd-list">
 <li class="dd-item ui-state-disabled">
							<div class="dd-handle">
                 <div class="question-first-div question-heading">Sr.No</div>
                 <div class="question-second-div question-heading">Question</div>
                 <div class="question-third-div question-heading">  </div>
 							</div>
           </li>
</ul>
<form action="{{ route('client.update-question-positions', [ 'client_id'=> $client->id, 'form_id' => $form_id,'script_id' => $script_id  ])}}" id="update_position" onsubmit="return false">
{{ csrf_field() }}
{{ method_field('POST') }}
<input type="hidden" name="script_id" value="{{$script_id}}">
 <ul class="dd-list" id="sortable">


           @foreach ($questions as $key => $question)
                        <li class="dd-item ">
                            <div class="dd-handle">
                                <div class="question-first-div">
                                <?php  $i++; ?>
                                 <span class="current_position" data-currentposition="{{$i}}">{{$i}}</span>

                                 <input type="hidden" class="new_positions"  name="question_fields[{{$question->id}}]" value="{{$question->position}}">

                                </div>
                                 <div class="question-second-div">{{ $question->question }}</div>
                                 <div class="question-third-div"> <a
                                        class="edit-link"
                                        href="{{  route('client.edit-script-question',['client_id' => $client->id,
                                        'form_id' => $form_id,
                                        'script_id' => $script_id,
                                        'question_id' => $question->id,
                                        'state' => $state,
                                        'commodity' => $commodity]) }}"
                                        data-toggle="tooltip"
                                        data-placement="top" data-container="body"
                                        title=""
                                        data-original-title="Edit/View Script">
                                         <?php echo getimage('images/edit.png'); ?>
                                        </a>
                                        <a
                                          class="delete-question delete-link"
                                          href="javascript:void(0)"
                                          data-toggle="tooltip"
                                          data-placement="top" data-container="body"
                                          title=""
                                          data-original-title="Delete Question"
                                          data-id="{{ $question->id }}"
                                          id="delete-question-{{ $question->id }}"
                                          data-questioname="{{ $question->question }}"
                                         >
                                         <?php echo getimage('images/cancel.png'); ?>
                                            </a>

                                            <a
                                       class="clone-script"
                                       href="javascript:void(0);"
                                       data-toggle="tooltip"
                                       data-placement="top" data-container="body"
                                       title=""
                                       data-id="{{ $question->id }}"
                                       id="clone-question-{{ $question->id }}"
                                       data-original-title="Clone Script"
                                       >
                                       <i class="fa fa-clone " aria-hidden="true"></i>
                                    </a>


                                            <span class="inline-block"><?php echo getimage('images/drag.png'); ?></span>
                                           </div>
                            </div>
                           </li>
                         @endforeach
                         @if(count($questions) == 0 )
                         <li class="dd-item">
                                <div class="dd-handle">
                                  <div class="question-first-div"> </div>
                                  <div class="question-second-div text-center">No Record Found </div>
                                  <div class="question-third-div">  </div>
                                </div>
                            </li>
                         @endif

					</ul>

 </form>



                           <div class="btnintable bottom_btns">
                           @if(count($questions) > 0 )
                              {!! $questions->appends(['state' => $state,'commodity' => $commodity])->links() !!}
                              @endif
                            </div>


                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>
</div>









  @include('client.forms.scriptquestions.questionpoup')
  @include('client.forms.scriptquestions.clonequestionpoup')

@endsection
