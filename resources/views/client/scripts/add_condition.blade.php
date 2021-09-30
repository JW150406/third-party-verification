<div class="team-addnewmodal v-star">
    <div class="modal fade" id="add_question_condition" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 683px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close model-condition-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>Conditions</h4>
                </div>
                <div class="message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        {{-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-green add-condi-btn mb10" id="condition-add-button">Add Condition </button>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="form-group condition-select-div" style="display:none;">
                                <select class="select2 form-control" id="condition-type" name="condition-type-name" >
                                    <option value="">Select Condition Type</option>
                                    <option value="tag">Tag</option>
                                    <option value="question">Question</option>
                                </select>
                                <span id="select2-condition-error-message" style='color:red;'></span>
                            </div>
                        </div> --}}
                        <div class="col-md-9 col-lg-9"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 show-question"></div>
                        <hr>
                        <div class= 'col-xs-12 col-sm-12 col-md-12'>
                            <p>This question will only be displayed if the following condition(s) are true.</p>
                        </div>

                        <div class="col-md-12">
                            <div class="btn-group btn-sales-all">
                                <button type="button" class="btn btn-green dropdown-toggle condition-dropdown" data-toggle="dropdown" aria-expanded="false" >
                                    Add Condition <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu upload-script-menu" role="menu">
                                    <li><a href="javascript:void(0);" type="button" onclick="changeConditionType('tag');">Tag</a></li>
                                    <li><a href="javascript:void(0);" type="button" onclick="changeConditionType('question');">Question</a></li>
                                </ul>
                            </div>
                        </div>

                        <input type="hidden" name="id" id="question-id">
                        <input type="hidden" name="positionId" id="question-position">
                        <input type="hidden" name="clientId" id="client-id" value="{{array_get($client, 'id')}}">
                        <input type="hidden" name="formId" id="form-id" value="{{array_get($form, 'id')}}">
                        <input type="hidden" name="scriptId" id="script-id" value="{{array_get($script, 'id')}}">
                        <input type="hidden" name="state" id="state-value" value="{{ !empty(app('request')->input('st')) ? strtoupper(app('request')->input('st')) : 'All' }}">
                        <div class="list-condition-div">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <button type="button" class="btn btn-and">AND</button><span class='first-tag-bg tag-name-highlight'></span>&nbsp;&nbsp;<span class='operator-tag operator-name-highlight'></span>&nbsp;&nbsp;<span class='tag-500 value-highlight'></span>
                            </div>
                        </div>
                        <div class="tag-condition-div" style="display:none;">
                            <div class="col-xs-12 col-sm-12 col-md-3 mt15 pr-0">
                                <div class="form-group">
                                    <select class="select2 form-control" id="tag-condition" name="tag-name">
                                        <option value="">Select Tag</option>
                                        <?php 
                                        $tags = (new App\Http\Controllers\Client\FormsController)->getAllTags(array_get($form,'id'));
                                        $tags = $tags->original['data'];?>
                                        @foreach($tags as $k=>$v)
                                        <?php $value = substr($v,1,strlen($v)-2) ?>
                                        <option value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <span id="select2-tag-error-message" style='color:red;' class="error-span"></span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 mt15 pr-0">
                                <div class="form-group">
                                    <select class="select2 form-control" id="condition-operator" name="operator-name" data-parsley-required='true' data-parsley-required-message="Please select operator" data-parsley-errors-container="#select2-operator-error-message">
                                        <option value="">Select Operator</option>
                                        @foreach(config()->get('constants.SCRIPT_QUESTION_CONDITION_OPERATOR') as $key => $val)
                                            <option value="{{$key}}">{{$val}}</option>
                                        @endforeach
                                    </select>
                                    <span id="select2-operator-error-message" style="color:red;" class="error-span"></span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 mt15 pr-0">
                                <div class="form-group">
                                    <input id="comparision-val" placeholder="Value" autocomplete="off" type="text" class="form-control" name="compare-value" value="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 mt15">
                                <div class="form-group">
                                <button class="btn add-condition-btn btn-green" id="tag-add-button" count="1">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="question-condition-div" style="display:none;">
                    
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                            <label for="timezone">Questions</label>
                                <select class="select2 form-control" id="question-list" name="quesition-name">
                                    <option value="">Select</option>
                                </select>
                                <span id="select2-question-error-message" style='color:red;' class="error-span"></span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="form-group">
                            <label for="timezone">Answers</label>
                                <select class="select2 form-control" id="answer-type" name="answer-name">
                                    <option value="">Select</option>
                                    <option value="{{config('constants.script_question_condition_value.yes')}}">Yes</option>
                                    <option value="{{config('constants.script_question_condition_value.no')}}">No</option>
                                </select>
                                <span id="select2-answer-error-message" style='color:red;' class="error-span"></span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                            <div class="form-group">
                            <button class="btn add-condition-btn btn-green" id="question-add-button"style="margin-top:20px;" count="1">Add</button>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script>
    function changeConditionType(type) {
        if(type == 'tag')
        {
            $('.tag-condition-div').css('display','block');
            $('.question-condition-div').css('display','none');
        }
        else if(type == 'question')
        {
            $('.question-condition-div').css('display','block');
            $('.tag-condition-div').css('display','none');
        }
        else
        {
            $('.question-condition-div').css('display','none');
            $('.tag-condition-div').css('display','none');
        }

        $('#select2-operator-error-message').html('');
        $('#select2-tag-error-message').html('');
        $('#select2-question-error-message').html('');
        $('#select2-answer-error-message').html('');
    }
$(document).ready(function(){
    
    $(document).on('click','.add_condition_class',function(){
        
        $('#question-id').attr('value',$(this).data('id'));
        $('.show-question').html('<strong>Q</strong>. '+$(this).closest('tr').find('td:eq(1)').html());
        $('.question-condition-div').css('display','none');
        $('.tag-condition-div').css('display','none');
        $('#condition-type').val(null).trigger('change');
        $('#add_question_condition').modal();
        getCondition();
        getQuestions();
        $('.condition-select-div').css('display','none');
        $('#question-list').html('<option value="">Select</option>');

        @if(array_get($script, 'scriptfor') != 'customer_verification')
            $('.condition-dropdown').css('display','none');
            $('.tag-condition-div').css('display','block');
        @else
            $('.condition-dropdown').css('display','block');
            $('.tag-condition-div').css('display','none');
        @endif
        // $('#select2-tag-error-message').html('');
        // $('#select2-operator-error-message').html('');
        // $('#select2-question-error-message').html('');
        // $('#select2-answer-error-message').html('');
    })
    $(document).on('click','.model-condition-close',function(){
        $('#question-table').DataTable().ajax.reload();
    })
    $(document).on('click','.add-condition-btn',function(){
        let btnId = $(this).attr('id');
        let count;
        let tagValue;
        let operatorValue;
        let compareValue;
        let operatorName;
        let conditionType;
        if(btnId == 'tag-add-button'){
            count = $(this).attr('count');
            count = parseInt(count)+1;
            tagValue = $("#tag-condition").val();
            conditionType = 'tag';
            operatorValue = $("#condition-operator").val();
            compareValue = $('#comparision-val').val();
            operatorName = $('#condition-operator').select2('data');
            if($('#tag-condition').val() == '')
            {
                $('#select2-tag-error-message').html('Please select tag');
                return false;
            }
            if($('#condition-operator').val() == '')
            {
                $('#select2-operator-error-message').html('Please select operator');
                return false;
            }
            $('#select2-tag-error-message').html('');
            $('#select2-operator-error-message').html('');
            
        }
        else if(btnId == 'question-add-button'){
            tagValue = $('#question-list').val();
            conditionType = 'question';
            operatorValue = 'is_equal_to';
            compareValue = $('#answer-type').val();
            if($('#question-list').val() == '')
            {
                console.log($('#question-list').val());
                $('#select2-question-error-message').html('Please select question');
                return false;
            }
            if($('#answer-type').val() == '')
            {
                $('#select2-answer-error-message').html('Please select answer');
                return false;
            }
            $('#select2-question-error-message').html('');
            $('#select2-answer-error-message').html('');
        }
        
        $.ajax({
            url:"{{route('clients.forms.script.questions.condition')}}",
            data:{'_token':'{{csrf_token()}}','tag':tagValue,'operator':operatorValue,'compare':compareValue,'questionId':$('#question-id').val(),'conditionType':conditionType},
            method: "post",
            success:function(data)
            {
                if(data.status == 'success'){
                    getCondition();
                }
                else
                printAjaxErrorMsg('This condition is already exist');
            }
        })
    })

    $('#question-list').change(function(){   
        // console.log($(this).select2('data')[0]['element']['attributes'][1]['nodeValue']);
        // console.log($(this).data('pid'));

        $('#question-position').attr('value',$(this).select2('data')[0]['element']['attributes'][1]['nodeValue']);
        if($('#question-list').val() == ''){
            $('#select2-question-error-message').html('Please select question');
        }else{
            $('#select2-question-error-message').html('');
        }
    })
    $('#answer-type').change(function(){        
        if($('#answer-type').val() == ''){
            $('#select2-answer-error-message').html('Please select answer');
        }else{
            $('#select2-answer-error-message').html('');
        }
        
    })
    $('#condition-operator').change(function(){        
        if($('#condition-operator').val() == ''){
            $('#select2-operator-error-message').html('Please select operator');
        }else{
            $('#select2-operator-error-message').html('');
        }
    })
    $('#tag-condition').change(function(){        
        if($('#tag-condition').val() == ''){
            $('#select2-tag-error-message').html('Please select tag');
        }else{
            $('#select2-tag-error-message').html('');
        }
    })

    $(document).on('click','.delete-condition-class',function(){
        $.ajax({
            url:"{{route('delete.script.questions.condition')}}",
            data:{'_token':'{{csrf_token()}}','conditionId':$(this).attr('id')},
            method: "post",
            success:function(data)
            {
                if(data.status=='success'){
                    getCondition();
                }
            }
        })    
    });
    
    $(document).on('change','#condition-type',function(){
        if($(this).val() == 'tag')
        {
            $('.tag-condition-div').css('display','block');
            $('.question-condition-div').css('display','none');
        }
        else if($(this).val() == 'question')
        {
            $('.question-condition-div').css('display','block');
            $('.tag-condition-div').css('display','none');
        }
        else
        {
            $('.question-condition-div').css('display','none');
            $('.tag-condition-div').css('display','none');
        }

        $('#select2-operator-error-message').html('');
        $('#select2-tag-error-message').html('');
        $('#select2-question-error-message').html('');
        $('#select2-answer-error-message').html('');
    });

    $(document).on('click','#condition-add-button',function(){
        $('.condition-select-div').css('display','block');
    })

    var tags = $.map($('#tag-condition option'), function(e) { if (e.value != "") return e.value; });
    $( "#comparision-val" ).autocomplete({
        // minLength:1,
        source: function (request, response) {
            var matches = $.map(tags, function (tag) {
                let searchStr = request.term.slice(request.term.indexOf('[')+1);
                searchStr = new RegExp( '^' + searchStr.toUpperCase());
                if(tag.match(searchStr) ) {
                    return tag;
                }
            });
            response(matches);
        },
        search: function () {
            // search enable if tag start with [ character
            if (this.value.indexOf('[') == -1) {
                return false;
            }
        },
        select: function( event, ui ) {
            // for set custom value
            this.value = '['+this.value;
            $( "#comparision-val" ).val( this.value.slice(0,this.value.indexOf('[')+1) + ui.item.value+']' );
            return false;
        }
    });
});

function getCondition()
{
    $.ajax({
        url:"{{route('retrive.script.questions.condition')}}",
        data:{'_token':'{{csrf_token()}}','questionId':$('#question-id').val()},
        method: "post",
        success:function(data)
        {
            let count = 0;   
            let div = '';
            $('.list-condition-div').html('');
            $.each(data.data,function(k,v){
                tagValue = v.tag;
                compareValue = v.comparison_value;
                if(v.comparison_value == null)
                    compareValue = '';
                if(v.condition_type == 'question')
                {
                    tagValue = 'Q'+v.position;
                    if(v.comparison_value == 1)
                        compareValue = v.positive_ans;
                    else
                        compareValue = v.negative_ans;
                }
                div += '<div class="col-xs-12 col-sm-12 col-md-12" id="'+count+'"><button type="button" class="btn btn-and">AND</button> <span class="first-tag-bg tag-name-highlight-'+(k+1)+'">'+tagValue+'</span>&nbsp;&nbsp;<span class="operator-tag operator-name-highlight-'+(k+1)+'">'+v.operator+'</span>&nbsp;&nbsp;<span class="tag-500 value-highlight-'+(k+1)+'">'+compareValue+'</span>&nbsp;&nbsp;<span id="'+v.id+'" class="delete-condition-class"><i class="fa fa-times" aria-hidden="true" style="color:red;cursor:pointer;"></i></span></div>';
                count = k+1;
            })
            $('.list-condition-div').append(div);
            $('.add-condition-btn').attr('count',count);
            $("#tag-condition").val(null).trigger('change');
            $("#condition-operator").val(null).trigger('change');
            $('#comparision-val').val('');
        }
    })
}

function getQuestions()
{
    $.ajax({
        url:"{{route('retrive.nested.script.questions.ajax')}}",
        method: 'post',
        data:{'_token':'{{csrf_token()}}','questionId':$('#question-id').val(),'clientId':$('#client-id').val(),'formId':$('#form-id').val(),'scriptId':$('#script-id').val(),'state':$('#state-value').val()},
        success:function(data){
            let quesDiv = '<option value="">Select</option>';
            $.each(data.data,function(k,v){
                quesDiv += '<option value='+v.id+' data-pid='+v.position+'>'+v.question+'</option>';
            })
            $('#question-list').html(quesDiv);
        }
    });
}
</script>
@endpush