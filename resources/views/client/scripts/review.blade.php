@extends('layouts.admin')
@section('content')
    <?php
    $breadcrum = array();
    $formEditLink = "#";
    if (Auth::user()->access_level == 'tpv') {
        $breadcrum[] = array('link' => route('client.index'), 'text' => 'Clients');        
        $formEditLink = route('client.contact-page-layout', array(array_get($client, 'id'), array_get($form, 'id')));
    }
    $breadcrum[] = array('link' => route('client.show', array_get($client, 'id')), 'text' => array_get($client, 'name'));
    $breadcrum[] = array('link' => route('client.show', array_get($client, 'id')) . "#EnrollmentForm", 'text' => 'Forms');
    $breadcrum[] = array('link' =>$formEditLink, 'text' => array_get($form, 'formname'));
    $breadcrum[] = array('link' => route('admin.clients.scripts.index', array(array_get($client, 'id'), array_get($form, 'id'))), 'text' => 'Scripts');
    $breadcrum[] = array('link' => 'javascript:void(0)', 'text' =>   Config::get('constants.scripts-new-name.' . array_get($script, 'scriptfor')));
    $breadcrum[] = array('link' => '', 'text' => 'Review');
    breadcrum($breadcrum);

    ?>

    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="client-bg-white">
                                @php $timeZone =Auth::user()->timezone; @endphp
                                <h5 class = 'script-title-display'>{{ ( Config::get('constants.scripts-new-name.' . array_get($script, 'scriptfor'))) }}</h5>
                                <h5>Created At: {{array_get($script, 'created_at')->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</h5>
                                @if (array_get($script, 'updated_at'))
                                    <h5>Last updated
                                        At: {{array_get($script, 'updated_at')->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat())}}</h5>
                                @endif
                                <h5>
                                    State: {{ !empty(app('request')->input('st')) ? strtoupper(app('request')->input('st')) : 'All' }}</h5>
                                <h5>Language: {{ Config::get('constants.script_languages.' . array_get($script, 'language')) }}</h5>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="table-responsive">
                                            <table id="question-table" class="table script-table  mt30">
                                                <thead>
                                                <tr>
                                                    <td class="w60">Sr.No.</td>
                                                    <td class="w430">Question</td>
                                                    <td></td>
                                                    @if (array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "identity_verification" || array_get($script, 'scriptfor') == "ivr_tpv_verification")
                                                        <td>Positive Answer</td>
                                                        <td>Negative Answer</td>
                                                        @if (array_get($script, 'scriptfor') == "customer_verification"  || array_get($script, 'scriptfor') == "ivr_tpv_verification")
                                                        <td >Introductory Question</td>
                                                        @endif
                                                        @if (array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "identity_verification")
                                                            <td class="w160">Verification Criteria</td>
                                                        @endif
                                                        
                                                    @endif
                                                    @if (array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "self_verification" || array_get($script, 'scriptfor') == "ivr_tpv_verification")
                                                    @if (array_get($script, 'scriptfor') == "self_verification")
                                                    <td >Introductory Question</td>
                                                    @endif
                                                        <td>Action</td>
                                                        <td>Conditions</td>
                                                            @if (array_get($script, 'scriptfor') == "customer_verification")
                                                            <td>Editable Tag</td>
                                                            <td>Continue On Negative</td>
                                                            @endif
                                                        <td>Multiple Enrollments</td>
                                                        @endif
                                                        <td style="display:none;">Id</td>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <!--end--col-8-->

                                </div>
                                <!--button-area--->
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="btn-group script-btns">
                                            <a href="{{ route('admin.clients.scripts.index', array(array_get($client, 'id'), array_get($form, 'id', 0))) }}"
                                               class="btn btn-red">Close</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('client.scripts.add_condition')
@endsection

@push('scripts')

    <script>
                @if(array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "identity_verification" || array_get($script, 'scriptfor') == "ivr_tpv_verification")
        var columnsArr = [{
                data: null,
                // name: 'position',
                searchable: false,
                orderable: false
            },
            
                {
                    data: 'question',
                    name: 'question',
                    searchable: false,
                    orderable: false
                },
                
                {
                    data: null,
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'positive_ans',
                    name: 'positive_ans',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'negative_ans',
                    name: 'negative_ans',
                    searchable: false,
                    orderable: false
                },
                @if(array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "ivr_tpv_verification")
                {
                    data: 'is_introductionary',
                    name: 'is_introductionary',
                    searchable: false,
                    orderable: false
                },
                @endif
                @if(array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "identity_verification")
                {
                    data: 'answer',
                    name: 'answer',
                    searchable: false,
                    orderable: false
                },
                @endif
                @if(array_get($script, 'scriptfor') == "customer_verification" || array_get($script, 'scriptfor') == "ivr_tpv_verification")
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'condition',
                    name: 'condition',
                    searchable: false,
                    orderable: false
                },
                @if(array_get($script, 'scriptfor') == "customer_verification")
                {
                    data: 'is_customizable',
                    name: 'is_customizable',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'negative_answer_action',
                    name: 'negative_answer_action',
                    searchable: false,
                    orderable: false
                },
                @endif
                {
                    data: 'multiple_enrollments',
                    name: 'multiple_enrollments',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    visible:false,
                    orderable: false
                },
               
                @endif

            ];
                @else
        var columnsArr = [{
                data: null,
                // name: 'position',
                searchable: false,
                orderable: false
            },
                {
                    data: 'question',
                    name: 'question',
                    searchable: false,
                    orderable: false
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false
                },
                @if(array_get($script, 'scriptfor') == "self_verification")
                {
                    data: 'is_introductionary',
                    name: 'is_introductionary',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'condition',
                    name: 'condition',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'multiple_enrollments',
                    name: 'multiple_enrollments',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    visible:false,
                    orderable: false
                },
                @endif
                
            ];
        @endif
        $(document).ready(function () {
            function getAllTags() {
                $.ajax({
                    url: "{{ route('ajax.getFormTags', array_get($form, 'id', 0)) }}",
                    type: "GET",
                    success: function (res) {
                        if (res.status === true) {
                            getQuestions(res.data);
                        } else {
                            getQuestions([]);
                        }
                    },
                    error: function () {
                        getQuestions([]);
                    }
                });
            }

            function getQuestions(tags) {
                var questionTable = $('#question-table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    lengthChange: true,
                    ordering: false,
                    dom: 'tr<"bottom"lip>',
                    ajax: {
                        url: "{{ route('admin.clients.forms.script.questions.index', array(array_get($client, 'id'), array_get($form, 'id', 0), array_get($script, 'id'))) }}",
                        data: {
                            "state": "{{ strtoupper(app('request')->input('st')) }}"
                        }
                    },
                    columns: columnsArr,
                    columnDefs: [{
                        "searchable": false,
                        "orderable": false,
                        "width": "5%",
                        "targets": 0,
                    }],
                    'fnDrawCallback': function () {
                        var table = $('#question-table').DataTable();
                        var info = table.page.info();
                        if (info.pages > 1) {
                            $('#question-table_info')[0].style.display = 'block';
                            $('#question-table_paginate')[0].style.display = 'block';
                        } else {
                            $('#question-table_info')[0].style.display = 'none';
                            $('#question-table_paginate')[0].style.display = 'none';
                        }
                        if(info.recordsTotal < 10) {
                            $('#question-table_length')[0].style.display = 'none';
                        } else {
                            $('#question-table_length')[0].style.display = 'block';
                        }
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        
                        if (aData.question) {
                            $("td:nth-child(3)", nRow).html("");
                            var match = aData.question.match(/[^[]+(?=\])/g);
                            if (match != null && match.length > 0) {
                                var array = [];
                                $.each(match, function (index, value) {
                                    if (!tags.includes("[" + value.trim().replace('-&gt;', '->').toUpperCase() + "]")) {
                                        array.push(value.trim().replace('-&gt;', '->'));
                                    }
                                    aData.question = aData.question.replace("[" + value + "]", "<span class='question-tag'>" + value + "</span>");
                                    $("td:nth-child(2)", nRow).html(aData.question);
                                });

                                if (array.length > 0) {
                                    $("td:nth-child(3)", nRow).html('<img src="{{ ('/images/alert-danger.png') }}"  width="13">');
                                    $("td:nth-child(3)", nRow).attr('data-toggle', "tooltip");
                                    $("td:nth-child(3)", nRow).attr('data-container', "body");
                                    $("td:nth-child(3)", nRow).attr('data-placement', "left");
                                    $("td:nth-child(3)", nRow).attr('title', array.toString());

                                } else {
                                    $("td:nth-child(3)", nRow).html('');
                                }
                            }

                        }

                        if (aData.answer) {
                            //$("td:nth-child(6)", nRow).html("");
                            var ansMatch = aData.answer.match(/[^[]+(?=\])/g);
                            if (ansMatch != null && ansMatch.length > 0) {
                                var ansArray = [];

                                 if (!tags.includes("[" + ansMatch[0].trim().replace('-&gt;', '->').toUpperCase() + "]")) {
                                        ansArray.push(ansMatch[0].trim().replace('-&gt;', '->'));
                                    }
                                    const answerTag = aData.answer.replace("[" + ansMatch[0] + "]", "<span class='question-tag'>" + ansMatch[0] + "</span>");
                                    $("td:nth-child(6)", nRow).html(answerTag);
                            }
                        }


                        var table = $('#question-table').DataTable();
                        var info = table.page.info();
                        console.log(aData);
                        if(aData.form_id != 0){
                            $("td:nth-child(1)", nRow).html(aData.position);
                        }
                        else{
                            $("td:nth-child(1)", nRow).html((info.start+iDisplayIndex));
                        }
                        // $("td:nth-child(1)", nRow).html((info.start+iDisplayIndex));
                        return nRow;
                    }
                });
            }

            getAllTags();
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#question-table').tooltip({trigger: 'manual'}).tooltip("option","show");
        });


    </script>

@endpush
