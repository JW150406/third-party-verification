<h3 class="agent-title">Lead verification <span style="font-size: 16px;margin-left: 40px">Customer name: </span> <span class="customer-name"></span></h3>
<div class="question">
    <div class="text-center" id="edit_text_response">
    </div>
</div>
<span id="questions_count" style="display: none">@php echo $questionCount - $introQuestions; @endphp</span>
<span id="intro_questions_count" style="display: none">@php echo $introQuestions; @endphp</span>
@php
    if($introQuestions == 0){
      $progress_percentage = round(100/($questionCount - $introQuestions));
    } else {
        $progress_percentage = 0;
    }
@endphp
<div class="questions-progress question-tab verification-question-text active-que">
    <div class="progress">
        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
             aria-valuenow="" aria-valuemin="0" aria-valuemax="100">
            <span class="popOver progress-tooltip" data-toggle="tooltip" data-placement="top" title=""></span>
        </div>
    </div>
    @php
        $childItr = 0;
        $isChild = false;
        $tagsLocal = $allQuestions[$leadId.'-tag'];
        $questionLocal = $allQuestions[$leadId.'-question'];
        $questions = $questionLocal;
        $questionItr = 0;
        $itrCount = 1;
        if($childLeads->count() != 0){
            $itrCount = $childLeads->count();
        }
    @endphp
    {{-- if child leads are there then questions and tags are retrived by child lead id --}}
    @for($itr = 0; $itr < $itrCount;)
    <?php
        if($childLeads->count() > 0){
        $v = $childLeads[$itr];
        $childItr++;
            if($childItr > 1){
                $itr++;
                $isChild = true;
                $tagsLocal = $allQuestions[$v->id.'-tag'];
                $questions = $allQuestions[$v->id.'-question'];
            }
        }
        else{
            $itr++;
        }
        ?>
    @forelse ($questions as $key => $question)
        <?php 
        //check whether current object is of child lead or not
        if($isChild == true){
            if($question['is_multiple'] != 1){
                continue;
            }
        }
            $i = $questionItr + 1; 
            $questionItr++;
            $conditions = $question['question_conditions'];

            $arrangedCond = [];
            if (!empty($conditions)) {
                foreach ($conditions as $condition) {
                    // $condition .= $condition->question_id . " = " . $condition->comparison_value;
                    // $conditionArr[] = $condition['question_id'] . " == " . $condition['comparison_value'];
                    $conditionArr = [];
                    $conditionArr['id'] = $condition['tag'];
                    $conditionArr['comp_val'] = $condition['comparison_value'];
                    $arrangedCond[] = $conditionArr;
                }
                // $arrangedCond = implode(" && ", $conditionArr);
            }
            
        ?>
        <input type="hidden" id="is_intro_question_{{$i}}" value="{{ array_get($question, 'is_introductionary') }}"  />
        <div id="question_main_div_{{$i}}" class="question question_div {{ array_get($question, 'is_introductionary') }}" data-conditions='<?php echo json_encode($arrangedCond); ?>'>
            <div class="text-left">
                <p class="question_wrapper">
                    @php
                    
                        $single_question = array_get($question, 'question');
                        $questionTip = array_get($question, 'answer');

                        $single_question = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                                return "[".trim(strtoupper($word[1]))."]";
                                }, $single_question);
                        $ques = strtr($single_question, $tagsLocal);

                        $questionTip = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                                return "[".trim(strtoupper($word[1]))."]";
                                }, $questionTip);
                        $questionTip = strtr($questionTip, $tagsLocal);

                        if(array_get($question, 'is_customizable')) {
                            $ques = str_replace('question-tag', 'question-tag edit-tag', $ques);
                        }
                        
                    @endphp

                    @if(array_get($question, 'is_introductionary'))
                        <span class="q-text q-telesale text-center"><b>@php echo nl2br(htmlspecialchars_decode($ques))@endphp</b></span>
                    @else

                        <span class="q-id"><b>Q{{$i - $introQuestions}}.</b></span> <span
                                class="q-text q-telesale">@php echo nl2br(htmlspecialchars_decode($ques)) @endphp</span>
                        {{--       
                        @if(array_get($question, 'is_customizable'))
                            <a href="javascript:void(0)" data-tag="{{array_get($question, 'answer')}}"
                               data-toggle="tooltip" data-placement="top" data-container="body" title=""
                               data-original-title="Edit Question" role="button" class="btn edit_question"><img
                                        src="https://newdev.tpv.plus/images/edit.png"></a>
                        @endif
                        --}}

                    @endif
                </p>

            </div>
            <div class="text-center question-btn">

                <a href="javascript:void(0)" id="quetion_yes_{{ array_get($question, 'id') }}"
                   data-id="{{ array_get($question, 'id') }}" data-nextelement="{{$i + 1}}" data-iteration="{{$i}}"
                   class="mr15 btn btn-green yes question_yes">{{ !empty(array_get($question, 'positive_ans')) && array_get($question, 'positive_ans') != NULL ? array_get($question, 'positive_ans') : 'Yes' }}</a>

                <a href="javascript:void(0)" class="btn btn-red question_no"
                   id="question_no_{{ array_get($question, 'id') }}" data-nextelement="{{$i + 1}}"
                   data-iteration="{{$i}}"
                   data-id="{{ array_get($question, 'id') }}" data-dec-action="{{ array_get($question, 'negative_answer_action', 0)}}">{{ !empty(array_get($question, 'negative_ans')) && array_get($question, 'negative_ans') != NULL ? array_get($question, 'negative_ans') : 'No' }}</a>

                @if($i > 1)
                    <a href="javascript:void(0)" id="leadpreviousQue" data-iteration="{{ $i }}"
                       class="pull-right btn btn-green back-btn previous_btn">Previous</a>
                @endif


            </div>
        </div>
        @if(!empty($questionTip))
            <div class="tip hidee" id="question_tip_{{$i}}"><i>@php echo nl2br(htmlspecialchars_decode($questionTip)) @endphp</i></div>
        @endif
    @empty
        @if($questionCount == 0)
        <div class="question">
            <div class="text-left">
                <p class="text-center">No Questions found !!</p>
            </div>
        </div>
        @endif
    @endforelse
    @endfor

    <div class="question" id="edit_field_container"></div>


</div>
<!--End-new questions flow--->


<script>
    $(document).ready(function () {

        $(".progress-bar").each(function () {
            each_bar_width = $(this).attr('aria-valuenow');
            $(this).width(each_bar_width + '%');
        });

        $('.progress-tooltip').tooltip({
            trigger: 'manual'
        }).tooltip('show');

        $(".edit_question").on('click', function () {
            var tag = $(this).data('tag');

            tag = tag.match(/[^[]+(?=\])/g);

            tag = tag[0].trim();

            $.ajax({
                type: 'post',
                url: "{{ route('get.field_question') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'tag': tag,
                    'lead': "{{ $leadId }}"
                },
                success: function (res) {
                    if (res.status === true) {
                        $('#edit_field_container').html(res.html)
                    } else {
                        $('#edit_text_response').html('<div class="alert alert-danger">' + res.message + '</div>');
                    }
                },
                error: function () {
                    $('#edit_text_response').html('<div class="alert alert-danger">Whoops, something went wrong please try again</div>');
                }
            });
        })
    });
</script>
<script>

    function changeVal(sourceElement, destElement) {
        if ($('input[name="is_service_address_same_as_billing_address"]:checked').val() == "yes") {
            $("#" + destElement).val($("#" + sourceElement).val());
        }
    }
</script>
