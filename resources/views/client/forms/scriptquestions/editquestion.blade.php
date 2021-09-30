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
     $breadcrum[] = array('link' =>  route('client.view-script-questions',['client_id' => $client->id, 'form_id' => $form_id, 'script_id' => $script_id,'state' => $state,'commodity' => $commodity]), 'text' =>  'Script Questions' );
     $breadcrum[] = array('link' => '', 'text' =>  'Edit Question' );
     breadcrum ($breadcrum);
    ?>
 
<script src="{{asset('js/isotope.pkgd.min.js')}}"></script>
<?php 
$added_fields = 0;
$formid = 0;
 ?>
 <div class="tpv-contbx scripts-ques add-ques">
   <div class="container">
      <h2>Edit Question</h2>
      <div class="addques-contbx">
         <div class="clearfix"></div>
         <br />
         <div class="col-md-12">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
               <p>{{ $message }}</p>
            </div>
            @endif
            <form class="form-horizontal"  role="form" method="POST"  action="">
                            {{ csrf_field() }} 
                            {{ method_field('POST') }}
                         
                          <div class="col-sm-6">   
                        
                            <div class="clearfix"></div>
                              <div class="name-wrapper" >
                                  <label class="control-label">Question Text</label>
                                  <textarea class="form-control" name="question" id="questiontext" placeholder="Enter text here">{{ $question_detail->question }}</textarea>
                                
                              </div> 
                              
                              @if( $script_detail->scriptfor =='customer_verification')
                              <div class="clearfix"></div>                              
                              <div class="col-sm-12" style="padding-left:0; padding-right:0" >
                                <label class="control-label">Answer </label>
                                <textarea type="text" name="answer" id="answer"   rows="5"  placeholder="Answer to verify"  class="form-control" >{{ $question_detail->answer}}</textarea>
                                
                              </div>
                               
                              <div class="clearfix"></div>                              
                              <div class="col-sm-12" style="padding-left:0; padding-right:0" >
                                <label class="control-label">TPV agent can edit this answer? </label>
                                <ul class="icheck-list">
                                                   <li style="width: auto;display: inline-block;margin: 10px 20px 10px 0px;"> 
                                                    <input type="radio" class="icheck" name="is_customizable" id="radio-1" value="1" @if($question_detail->is_customizable == '1') checked='checked' @endif>
                                                    <label for="radio-1">Yes</label>
                                                </li>
                                                   <li style="width: auto;display: inline-block;margin: 10px 20px 10px 0px;"> 
                                                    <input type="radio" class="icheck" name="is_customizable" id="radio-2" value="0"   @if($question_detail->is_customizable == '0' || $question_detail->is_customizable == '' ) checked='checked' @endif>
                                                    <label for="radio-2">No</label>
                                                </li>
                                                                  
                                    </ul>
                                
                              </div>
                              <div class="clearfix"></div>
                              <div class="col-sm-6" style="padding-left:0" >
                                  <label class="control-label">Positive </label>
                                  <input type="text" name="positive_ans" id="positive_ans" value="{{ $question_detail->positive_ans}}" required class="form-control" >
                                  <div class="clearfix"></div>
                                  <div class="positive-tags">
                                      <span class="addtag grid-item"> <strong data-rel="Yes">[Yes]</strong> </span>
                                      <span class="addtag grid-item"> <strong data-rel="Agree">[Agree]</strong> </span>
                                      <span class="addtag grid-item"> <strong data-rel="Understand">[Understand]</strong> </span>
                                      <span class="addtag grid-item"> <strong data-rel="Correct">[Correct]</strong> </span>
                                      
                                  </div>
                                
                              </div> 
                              <div class="col-sm-6" style="padding-right:0" >
                                  <label class="control-label">Negative </label>
                                  <input type="text" name="negative_ans" id="negative_ans" value="{{ $question_detail->negative_ans }}" required class="form-control"  >
                                  <div class="clearfix"></div>
                                  <div class="negative-tags">
                                      <span class="addtag grid-item negative-tag"> <strong data-rel="No">[No]</strong> </span>
                                      <span class="addtag grid-item negative-tag"> <strong data-rel="Disagree">[Disagree]</strong> </span>
                                      <span class="addtag grid-item negative-tag"> <strong data-rel="Don't understand">[Don't understand]</strong> </span>
                                      
                                      
                                  </div>
                                
                              </div> 
                              @else
                             <input type="hidden" name="positive_ans" id="positive_ans" value="{{ $question_detail->positive_ans}}" >
                             <input type="hidden" name="negative_ans" id="negative_ans" value="{{ $question_detail->negative_ans }}"  >
                             <input type="hidden" name="positive_ans" id="positive_ans" value="" >
            
                                  <input type="hidden" name="is_customizable" id="is_customizable" value="0"  >
                                     <input type="hidden" name="answer" id="answer" value=""  >

                             @endif
                            
                              <div class="clearfix"></div>
                             
                              <div class="" style="margin-top:20px;">
                                <button class="savefield btn btn-green">update <span class="add"><?php echo getimage('images/update_w.png'); ?></span> </button> 
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="name-wrapper tpvtags" >
                                @foreach($tags as $tag)
                                <span class="addtag grid-item"> <strong>{{$tag}}</strong> </span> 
                                @endforeach
                              </div>
                          </div>
                           
                       </form> 
           </div>
        </div>
    </div>
</div> 
<style>
 
.grid-item { display: inline-block;
background: #00a651;
color: #fff;
padding: 5px 12px;
margin: 5px 0 4px 0;
border: 1px transparent;
border-radius: 4px; }
</style>
 
 
  @endsection

 
