 <!--new-design-start-Declined Lead-options---->
<div class="col-sm-12">
  <div class="declined_lead-wrapper">
    <div class="declined_lead-options">
      <div class="form-group radio-btns pdt0">
        <h3>Lead Declined</h3>
        <p></p>

        
        @if(!empty($questions->question))

              @php
                  $single_question = $questions->question;

                  $single_question = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                          return "[".trim(strtoupper($word[1]))."]";
                          }, $single_question);
                  $ques = strtr($single_question, $tags);
              @endphp
         
          <div id="" class="question question_div">
        <div class="text-left">
            <p class="question_wrapper">
                <span class="q-text">@php echo nl2br(htmlspecialchars_decode($ques)) @endphp</span>
            </p>
            
        </div>
        <div class="text-center question-btn">

{{--            <a href="javascript:void(0)" id="lead-decline-hangup"  class="mr15 btn btn-green">Hang Up</a>--}}


        </div>
    </div>
       
        @else
    <div class="question">
        <div class="text-left">
            <p class="text-center">No Questions found !!</p>
        </div>
    </div>
    @endif

     
      </div>
    </div>
  </div>
</div>
<!--end-Declined Lead-options---->