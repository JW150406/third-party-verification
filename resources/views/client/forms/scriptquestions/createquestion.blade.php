

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
     $breadcrum[] = array('link' =>  route('client.view-script-questions',['client_id' => $client->id, 'form_id' => $form_id, 'script_id' => $script_id,'state' => $state, 'commodity' => $commodity]), 'text' =>  'Script Questions' );
     $breadcrum[] = array('link' => '', 'text' =>  'New Question' );
     breadcrum ($breadcrum);
    ?>
<?php 
   $added_fields = 0;
   $formid = 0;
    ?>
<div class="tpv-contbx scripts-ques add-ques">
   <div class="container">
      <h2>New Question</h2>
      <div class="addques-contbx">
         <div class="clearfix"></div>
         <br />
         <div class="col-md-12">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
               <p>{{ $message }}</p>
            </div>
            @endif
            <form role="form" method="POST"  action="">
               {{ csrf_field() }} 
               {{ method_field('POST') }}
               <input type="hidden" class="clientid" name="client_id" value="{{$client_id}}">
               <input type="hidden" class="form_id" name="form_id" value="{{$form_id}}">
               <input type="hidden" class="state" name="state" value="{{$state}}">
               <input type="hidden" class="commodity" name="commodity" value="{{$commodity}}">
               <div class="col-md-5">
                  <div class="form-group" >
                     <label class="control-label">Question Text</label>
                     <textarea class="form-control" name="question" rows="5" id="questiontext" placeholder="Enter text here"></textarea>
                  </div>
                  @if( $script_detail->scriptfor =='customer_verification')
                  <div class="clearfix"></div>
                  <div class="col-sm-12" style="padding-left:0; padding-right:0" >
                     <label class="control-label">Answer </label>
                     <textarea type="text" name="answer" id="answer" value=""  rows="5"  placeholder="Answer to verify"  class="form-control" ></textarea>
                    
                  </div>
                  <div class="clearfix"></div>                              
                              <div class="col-sm-12" style="padding-left:0; padding-right:0; margin-top:15px;" >
                                <label class="control-label">TPV agent can edit this answer? </label>
                                <ul class="icheck-list">
                                                   <li style="width: auto;display: inline-block;margin: 10px 20px 10px 0px;"> 
                                                    <input type="radio" class="icheck" name="is_customizable" id="radio-1" value="1">
                                                    <label for="radio-1">Yes</label>
                                                </li>
                                                   <li style="width: auto;display: inline-block;margin: 10px 20px 10px 0px;"> 
                                                    <input type="radio" class="icheck" name="is_customizable" id="radio-2" value="0"   checked>
                                                    <label for="radio-2">No</label>
                                                </li>
                                                                  
                                    </ul>
                                
                              </div>
                  <div class="clearfix"></div>
                  <div class="col-sm-6" style="padding-left:0" >
                     <label class="control-label">Positive </label>
                     <input type="text" name="positive_ans" id="positive_ans" value="" required class="form-control" >
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
                     <input type="text" name="negative_ans" id="negative_ans" value="" required class="form-control"  >
                     <div class="clearfix"></div>
                     <div class="negative-tags">
                        <span class="addtag grid-item negative-tag"> <strong data-rel="No">[No]</strong> </span>
                        <span class="addtag grid-item negative-tag"> <strong data-rel="Disagree">[Disagree]</strong> </span>
                        <span class="addtag grid-item negative-tag"> <strong data-rel="Don't understand">[Don't understand]</strong> </span>
                        <span class="addtag grid-item negative-tag"> <strong data-rel="In-correct">[In-correct]</strong> </span>
                     </div>
                  </div>
                  @else
                  <input type="hidden" name="positive_ans" id="positive_ans" value="" >
                  <input type="hidden" name="negative_ans" id="negative_ans" value=""  >
                  <input type="hidden" name="is_customizable" id="is_customizable" value="0"  >
                  <input type="hidden" name="answer" id="answer" value=""  >
                  @endif
                  <div class="clearfix"></div>
                  <div class="" style="margin-top:20px;">
                     <button class="savefield btn btn-green">Save<span class="add"><?php echo getimage('images/save.png'); ?></span>  </button> 
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="drag-drop-text">Drag and drop tags or click on tag to insert</div>
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

