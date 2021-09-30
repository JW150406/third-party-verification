 <div id="verifylead" class="question" style="display: none;">
	      <input type="hidden" name="ref" value="{{$reference_id}}" id="reference_id_to_update">
        {{ csrf_field() }}
          <div class="text-left">
            <p class="question_wrapper text-center">
                Verify sale? <br/>
				If you submit this lead it will be verified. This can not be undone.
            </p>
        </div>
        <input type="hidden" id="last-iteration" name="iteration" value="">
          <div class="text-center question-btn">
			<button id="verify-lead" type="button" class="mr15 btn btn-green">Verify</button>
		   <a href="javascript:void(0)" id="lastQuestionsShow" class="mr15 btn btn-red">Back</a>
        </div>
    </div>
