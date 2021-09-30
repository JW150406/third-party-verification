@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array();
    if (Auth::user()->access_level == 'tpv') {
        $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');
    }


    $breadcrum = array(
        array('link' => route('client.index'), 'text' =>  'Clients'),
        array('link' => route('client.show', array_get($client, 'id')), 'text' =>  array_get($client, 'name')),
        array('link' => route('client.show', array_get($client, 'id')) . "#EnrollmentForm", 'text' =>  'Forms'),
        // array('link' => route('client.contact-page-layout', [array_get($client, 'id'), array_get($form, 'id')]), 'text' =>  array_get($form, 'formname')),
        array('link' => route("admin.clients.scripts.index", [array_get($client, 'id'), array_get($form, 'id')]), 'text' =>  'Scripts'),
        array('link' => '', 'text' =>  'Import Script Questions')
    );
    breadcrum($breadcrum);
    ?>
    <div class="tpv-contbx edit-agentinfo">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="col-xs-12 col-sm-12 col-md-12 pdlr0">
                        <div class="message"></div>
                            <div class="client-bg-white">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-6">

                                        @if($upload_id == 1)
                                            @php $title = 'Bulk Upload Scripts'; @endphp
                                        @else
                                            @php $title = 'Import Single Script'; @endphp
                                        @endif

                                        <h1 style="padding-left:15px;">{{array_get($form, 'formname')}} - {{$title}} </h1>

                                        @if ($message = Session::get('success'))
                                            <div class="alert alert-success">
                                                <span>{{ $message[0] }}</span>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            {{\Session::forget('success')}}
                                        @endif
                                        @if($message = Session::get('error'))
                                            <div class="alert alert-danger">
                                                <p>{{ $message }}</p>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <!-- <span aria-hidden="true">&times;</span> -->
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- <div  class="col-xs-12 col-sm-3 col-md-3"></div> -->
                                    <div class="col-xs-12 col-sm-3 col-md-3">
                                        <div class="tags-table">
                                            <h1>Script Tags</h1>
                                        </div>

                                    </div>
                                    <div class="col-xs-12 col-sm-3 col-md-3">
                                        <div class="btn-group pull-right tags-table mr15">
                                            <a href="" type="button" class="btn btn-green" id="export-tags">Export
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="agent-detailform script-import-div">
                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                <div class="script-import-form">
                                                    <form id="import-form" action="{{ route('import.script.que') }}" method="post" enctype="multipart/form-data" data-parsley-validate>

                                                        @csrf
                                                        <input type="hidden" name="form" id="form_hidden_id" value ="{{$form->id}}" class="form_hidden">
                                                        <input type="hidden" name="client" id="client_hidden_id" value ="{{$client->id}}">
                                                        <input type="hidden" name="upload_script" id="" value ="{{$upload_id}}">
                                                        <input type="hidden" name="script_select" value="" class="scriptSelect">


                                                        <div class="form-group">
                                                            <label class="control-label"
                                                                   for="type">Select Script</label>
                                                            @php
                                                                $formScripts = config()->get('constants.scripts-new-name');

                                                            @endphp
                                                            <select class="select2 form-control validate required utilityoptions script_tags_select script-title-display"
                                                                    id="type"
                                                                    name="type"
                                                                    data-parsley-trigger="focusout"
                                                                    data-parsley-required='true'
                                                                    data-parsley-required-message="This field is required"
                                                                    data-parsley-errors-container="#script-error-message">
                                                                <option value="" fid="-1">Select Script</option>
                                                                <optgroup label="Verification Scripts">
                                                                    @foreach ($formScripts as $key => $formScript)
                                                                        @if(!in_array($key,config('constants.general_scripts')))
                                                                            @if($key == 'customer_verification' || $key == "self_verification" || $key == "self_outbound_verification" || $key == "ivr_tpv_verification")
                                                                                @php $fid ="$form->id" @endphp
                                                                            @else
                                                                                @php $fid ="0"; @endphp
                                                                            @endif
                                                                            <option value="{{$key}}" fid ="{{$fid}}"  @if(old('type') == $key) selected @endif>{{$formScript}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </optgroup>
                                                                <optgroup label="General Scripts">
                                                                    @foreach ($formScripts as $key => $formScript)
                                                                        @if(in_array($key,config('constants.general_scripts')))

                                                                            <option value="{{$key}}" fid ="0" @if(old('type') == $key) selected @endif>{{$formScript}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </optgroup>

                                                            </select>
                                                            <span id='script-error-message'></span>
                                                            @if ($errors->has('type'))
                                                                <span class="help-block">
                                                            <strong>{{ $errors->first('type') }}</strong>
                                                        </span>
                                                            @endif
                                                        </div>
                                                        @if($upload_id == 2)
                                                            <div class="form-group">
                                                                <label class="control-label "for="type">Language</label>

                                                                <div class="form-group radio-btns pdt10">
                                                                    <label class="radio-inline language-radio-label">
                                                                        <input type="radio" class=" language-radio"  name="language" value ="en" required data-parsley-error-message="Please select atleast one option"  data-parsley-errors-container="#radio-errors" checked> English
                                                                    </label>
                                                                    <label class="radio-inline language-radio-label">
                                                                        <input type="radio" class=" language-radio" data-parsley-trigger="focusout" name="language" value ="es"data-parsley-required='true'  @if(old('language') == 'es') checked @endif> Spanish
                                                                    </label>
                                                                </div>
                                                                <span id="radio-errors">
                                                            </span>
                                                                @if ($errors->has('language'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('language') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>

                                                            <div class="form-group stateDiv">
                                                                <label class="control-label stateDiv"for="type">State</label>
                                                                <select class="select2 form-control required utilityoptions stateSelect stateDiv"
                                                                        id="state"
                                                                        name="state"
                                                                        data-parsley-trigger="focusout"
                                                                        data-parsley-required='true'
                                                                        data-parsley-required-message="Please select state"
                                                                        data-parsley-errors-container="#state-errors">
                                                                    <option value="" sid="-1">Select State</option>

                                                                </select>
                                                                <span id ="state-errors" class="stateDiv"></span>
                                                                @if ($errors->has('state'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('state') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>

                                                        @endif
                                                        <div class="form-group dropzone-container">
                                                            <label class="control-label"
                                                                   for="type">Select XLSX File</label>
                                                        <!-- <input id="file-input" class="form-control" style="display:block;" name="csv" type="file" data-parsley-required='true' data-parsley-required-message="Please upload xlsx file"/ accept=".xlsx , .xls">

                                                    @if ($errors->has('csv'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('csv') }}</strong>
                                                        </span>
                                                    @endif -->
                                                            <div class="dropzone files-container " id="upload-file">
                                                                <div class="fallback">
                                                                    <input name="file" type="file" data-parsley-required='true' />
                                                                </div>
                                                            </div>
                                                            @include('preview-dropzone')
                                                            <input type="hidden" name="csv">
                                                            <span class = "help-block"></span>
                                                        </div>
                            
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                                <span><a href="{{route('download.sample.file',[array_get($client, 'id'),array_get($form, 'id'),old('type')])}}" class="script-tag-link theme-color" name="" id="download-sample" style="display:none; font-size: 11px; font-weight: 500;">Download Sample File</a></span>

                                                            </div>
                                                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                                <span> <a href="{{route('download.sample.file',[array_get($client, 'id'),array_get($form, 'id'),old('type'),old('language'),old('state')])}}" class="script-tag-link theme-color" name="" id="download-sample-file" style="display:none; text-align: right; padding-right: 8px; font-size: 11px; font-weight: 500;">Export Current Script</a></span>
                                                            </div>
                                                        </div>
                                                        <button id="upload-btn" type="button" class="btn btn-green">Save</button>

                                                    </form>

                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                <input type="hidden" id="script-tag-length" value="">
                                                <div class="table-responsive tags-table" style="margin-top:9px;width: 100%;">
                                                    <table class="table ld-report table" id="script-tag-table" style="width: 100%;">
                                                        <thead>
                                                        <tr class="acjin script-tags-thead" style="height:35px;text-align:center;">
                                                            <td style="width:70px; position:sticky;top:0;">Sr No.</td>
                                                            <td style="position:sticky;top:0;">Label</td>
                                                            <td style="position:sticky;top:0;">Tags</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody id ="tags-display-table" style="text-align:center;" class="scroller" > 
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                        </div>
                                      
                                    </div>
                                </div>
                            <div class="messageErr">
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<style>

tbody {
    display:block;
    height:calc(100vh - 150px);
    overflow:auto;
}
thead, tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;/* even columns width , fix width of table too*/
}
tbody tr td:first-child{
    width:70px;
}

    </style>
@endpush
    <script>
        
        function callAjax() {
            form_id = $(".form_hidden").val();
            client_id = $("#client_hidden_id").val();
            scriptId = $(".script_tags_select option:selected").attr('fid');
            
            if(scriptId != '-1'){
                $.ajax({
                    url: "{{url('scripts/')}}/" + $(".script_tags_select option:selected").attr('fid') + "/tags-category",
                    method: "get",
                    success:function(data)
                    {
                        let table = '';
                        let i = 0;
                        console.log(data);
                        if (data.status) {                            
                            $.each(data.data,function(key,val){  
                                table += '<tr class="permission-group"><th colspan='+3+' style="text-align:center;">'+ key +"</th></tr>";
                                $.each(val,function(k,v){
                                    table += "<tr><td>"+(++i)+"</td><td>" +v['label']+"</td><td>"+v['tags']+"</td></tr>";
                                });
                            });
                            $('#tags-display-table').html(table);
                        }
                        $('#export-tags').css('display','block');
                    }

                });
            }   
            else{
                $("#tags-display-table").html("<tr><td colspan=3>No Record Found</td></tr>");
                $('#export-tags').css('display','none');
            }
        }
        $(document).ready(function() {
            callAjax();
            $(".tags-table").css("display", 'none');

            var form_id;
            var data_tag = [];
            var i = 0;

            if ('{{old("type")}}' != '') {
                $(".help-block").html('');
                // isScript = checkStateExport();
                // if(isScript == 'true')
                // {
                //     $("#download-sample").css('display','block');
                //     $("#download-sample-file").css('display','block');
                // }
                if ('{{old("state")}}' != 'ALL') {
                    data = getStateAjax();
                    if (data.status == "success") {
                        $('.stateDiv').css('display', 'block');
                        $('.stateSelect').attr('data-parsley-required', 'true');
                        states = data.data;
                        var script_id = $(".script_tags_select option:selected").val();
                        option = "<option value = '' sid = '-1'>Select State</option>";
                        if (script_id == 'customer_verification' || script_id == "self_verification" || script_id == "self_outbound_verification" || script_id == "ivr_tpv_verification") {
                            $('.scriptSelect').attr('value', '');
                            $.each(states, function(k, v) {
                                if ('{{old("state")}}' == v.state)
                                    option += "<option value = " + v.state + " selected>" + v.state + "</option>";
                                else
                                    option += "<option value = " + v.state + ">" + v.state + "</option>";
                            });

                        }
                        $(".stateSelect").html(option);

                    }
                } else {
                    option = "<option value = 'ALL'>Select State</option>";
                    $('.stateDiv').css('display', 'none');
                    $('.scriptSelect').attr('value', 'all');
                    $('.stateSelect').removeAttr('data-parsley-required');
                }
                checkStateExport();
                $(".help-block").html('');
                alert();
            }
            $(".script_tags_select").change(function() {
                callAjax();
                route = "";
                var script = $(".script_tags_select option:selected").val();
                form_id = $(".script_tags_select option:selected").attr('fid');
                client = $('#client_hidden_id').val();
                form = "{{$form->id}}";
                upload_id = "{{$upload_id}}";
                $("#download-sample").css('display', 'block');
                sampleRoute = "{{route('download.sample',['script','upload_type'])}}";
                sampleRoute = sampleRoute.replace('script', script);
                sampleRoute = sampleRoute.replace('upload_type', upload_id);
                $("#download-sample").attr('href', sampleRoute);

                // isScript = checkStateExport();
                if (upload_id == 2) {
                    route = "{{route('download.sample.file',['clientid','formid','script','language','state'])}}";
                    checkStateExport();
                    // if(isScript == 'true')
                    // {
                    //     $("#download-sample-file").css('display','block');
                    // }
                    // else
                    //     $("#download-sample-file").css('display','none');
                } else {
                    route = "{{route('download.sample.file',['clientid','formid','script'])}}";
                    checkStateExport();
                    if (form_id != "-1") {

                        $("#download-sample-file").css('display', 'block');
                    } else {
                        $("#download-sample-file").css('display', 'none');
                    }
                }
                route = route.replace('clientid', client);
                route = route.replace('formid', form);
                route = route.replace('script', script);
                radio_val = $("input[name='language']:checked").val();

                if (radio_val == "en" || radio_val == "es") {
                    route = route.replace('language', radio_val);
                }
                route = route.replace('script', script);
                route = route.replace('clientid', client);
                route = route.replace('formid', form);

                // set export button route
                exportRoute = "{{route('export.tags',['client_id','form_id','script_tag'])}}";
                exportRoute = exportRoute.replace('client_id', client);
                exportRoute = exportRoute.replace('form_id', $(".script_tags_select option:selected").attr('fid'));
                exportRoute = exportRoute.replace('script_tag', script);
                $("#export-tags").attr('href', exportRoute);

                // var scriptTable = $('#script-tag-table').DataTable();
                // scriptTable.ajax.url("{{url('scripts/')}}/" + $(".script_tags_select option:selected").attr('fid') + "/tags-category").load();

                $(".tags-table").css("display", 'block');
                //ajax function for get state
                data = getStateAjax();

                if (data.status == "success") {
                    $('.stateDiv').css('display', 'block');
                    $('.stateSelect').attr('data-parsley-required', 'true');
                    states = data.data;
                    var script_id = $(".script_tags_select option:selected").val();
                    option = "<option value = '' sid = '-1'>Select State</option>";
                    if (script_id == 'customer_verification' || script_id == "self_verification" || script_id == "self_outbound_verification" || script_id == "ivr_tpv_verification") {
                        $('.scriptSelect').attr('value', '');
                        $.each(states, function(k, v) {

                            option += "<option value = " + v.state + ">" + v.state + "</option>";
                        });
                        $("#download-sample-file").attr('href', route);
                    } else {
                        option = "<option value = 'ALL'>Select State</option>";
                        route = route.replace('/state', "/ALL");
                        $("#download-sample-file").attr('href', route);
                        $('.stateDiv').css('display', 'none');
                        $('.scriptSelect').attr('value', 'all');

                        $('.stateSelect').removeAttr('data-parsley-required');
                        if (upload_id == 2) {

                            if ((radio_val == "en" || radio_val == "es") && script_id != "") {
                                $("#download-sample-file").css('display', 'block');
                            } else {
                                $("#download-sample-file").css('display', 'none');
                            }
                        }

                    }
                    if (script_id == "") {
                        $('.stateDiv').css('display', 'block');
                        $('.stateSelect').attr('data-parsley-required', 'true');
                    }
                    $(".stateSelect").html(option);

                }

            });


            $(".language-radio").click(function() {
                language = $(this).val();
                var script_id = $(".script_tags_select option:selected").val();
                route = $("#download-sample-file").attr('href');
                route = route.replace('language', language);
                search = route.search("es/", language);
                if (search >= 0) {
                    route = route.replace("es/", language + "/");
                }
                search_en = route.search("en/", language);
                if (search_en >= 0) {
                    route = route.replace("en/", language + "/");
                }

                // isScript = checkStateExport();
                if (script_id != "" && $(".stateSelect option:selected").attr('sid') != "-1") {
                    checkStateExport();
                    // $("#download-sample-file").css('display','block');
                    route = route.replace("state", $(".stateSelect option:selected").val());
                } else {
                    if ('{{old("type")}}' != "") {
                        checkStateExport();
                        // $("#download-sample-file").css('display','block');
                    } else
                        $("#download-sample-file").css('display', 'none');
                }
                $("#download-sample-file").attr('href', route);

            });
            $('.stateSelect').change(function() {
                form_id = $(".script_tags_select option:selected").attr('fid');
                radio = $("input[name='language']:checked").val();
                var state;
                if ($(this).attr('sid') == "-1") {
                    $("#download-sample-file").css('display', 'none');
                }
                if (form_id != -1 && (radio == "es") || radio == 'en') {
                    route = $("#download-sample-file").attr('href');
                    state = $(".stateSelect option:selected").val();
                    if (state != "") {
                        if (route.search("state", route) > 0) {
                            route = route.replace('state', state);
                        } else {
                            routeArr = route.split('/');
                            route = route.replace("/" + routeArr[routeArr.length - 1], "/" + state);
                        }
                        $("#download-sample-file").attr('href', route);
                        upload_id = "{{$upload_id}}";

                        // isScript = checkStateExport();
                        if (upload_id == 2) {
                            checkStateExport();
                            // $("#download-sample-file").css('display','block');
                        } else {
                            $("#download-sample-file").css('display', 'none');
                        }
                    } else {
                        $("#download-sample-file").css('display', 'none');
                    }
                }
            });
        })

        function getStateAjax() {
            var data;
            $.ajax({
                url: "{{route('get.state')}}",
                method: "get",
                async: false,
                success: function(res) {
                    data = res;
                },
                error: function() {
                    data = '';
                }
            });
            return data;
        }

        function checkStateExport() {
            var data;
            $.ajax({
                url: '{{route("check-state-script")}}',
                data: {
                    'state': $(".stateSelect option:selected").val(),
                    'client': '{{array_get($client,"id")}}',
                    'form': '{{array_get($form,"id")}}',
                    'scriptType': $(".script_tags_select option:selected").val(),
                    'language': $("input[name='language']:checked").val()
                },
                method: 'get',
                // async:false,
                success: function(res) {
                    if (res == 'true') {
                        $("#download-sample").css('display', 'block');
                        $("#download-sample-file").css('display', 'block');
                    } else {
                        $("#download-sample-file").css('display', 'none');
                    }
                    data = res;
                }
            })
            return data;
        }
    </script>

@endsection
@push('scripts')
    <script>
        function submitForm() {
            $("#update-profile").prop("disabled", true);
            let formData = $('#import-form').serializeArray();
            $.ajax({
                url: $("#import-form").attr('action'),
                method:'POST',
                data: formData,
                success:function(res) {
                    successHandler(res);
                },
                error: function(response) {
                    errorHandler(response);
                }
            });
        }

        Dropzone.autoDiscover = false;
        //Dropzone related code
        var target = "#upload-file";

        function dropzoneCount() {
            var filesCount = $("#previews > .dz-success.dz-complete").length;
            return filesCount;
        }

        function fileType(fileName) {
            var fileType = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
            return fileType[0];
        }

        var previewNode = document.querySelector("#cust-dropzone-template"), // Dropzone template holder
            warningsHolder = $("#warnings"); // Warning messages' holder

        previewNode.id = "";

        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        var insDropzone = new Dropzone("div#upload-file", {
            url: $("#import-form").attr('action'),
            autoProcessQueue: false,
            paramName: "csv",
            parallelUploads: 1,
            timeout: 0,
            maxFiles: 1,
            uploadMultiple: false,
            //acceptedFiles: 'image/*',
            previewTemplate: previewTemplate,
            previewsContainer: "#previews",
            clickable: true,
            createImageThumbnails: true,
            dictDefaultMessage: "Drop files here to upload, Or Browse", // Default: Drop files here to upload
            dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.", // Default: Your browser does not support drag'n'drop file uploads.
            dictInvalidFileType: "You can't upload files of this type.", // Default: You can't upload files of this type.
            dictCancelUpload: "Cancel upload.", // Default: Cancel upload
            dictUploadCanceled: "Upload canceled.", // Default: Upload canceled.
            dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?", // Default: Are you sure you want to cancel this upload?
            dictRemoveFile: "Remove file", // Default: Remove file
            dictRemoveFileConfirmation: null, // Default: null
            dictMaxFilesExceeded: "You can not upload any more files.", // Default: You can not upload any more files.
            dictFileSizeUnits: {tb: "TB", gb: "GB", mb: "MB", kb: "KB", b: "b"},
            init: function () {
                
                let objDropzone = this;
                //for Dropzone to process the queue (instead of default form behavior):
                document.getElementById("upload-btn").addEventListener("click", function(e) {
                    //Make sure that the form isn't actually being sent.
                    $("#import-form").parsley().validate();
                    if ($("#import-form").parsley().isValid()) {
                        if (objDropzone.getUploadingFiles().length === 0 && objDropzone.getQueuedFiles().length === 0) {
                            
                            $(".help-block").html('');
                            errorDropzone();
                            
                        } else {
                            $(".help-block").html('');
                            // submitForm();
                                e.preventDefault();
                                e.stopPropagation();
                                objDropzone.processQueue();   
                            }
                    }
                    else
                    {
                        if (objDropzone.getUploadingFiles().length === 0 && objDropzone.getQueuedFiles().length === 0) {
                            errorDropzone();
                        }
                    }
                    // }
                });
            }
        });

        insDropzone.on('sending', function(file, xhr, formData) {
            $("#upload-btn").prop("disabled", true);
            let data = $('#import-form').serializeArray();
            $.each(data, function (key, el) {
                if (el.name == "_token") {
                    formData.append("_token", CSRF_TOKEN);
                } else {
                    formData.append(el.name, el.value);
                }
            });
        });

        insDropzone.on('success', function (file, res) {
            this.emit("complete", file);
            successHandler(res);
        });

        insDropzone.on('error', function(file, err, xhr) {
            $("#upload-btn").prop("disabled", false);
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                if (err.status == 'dataErrors') {
                    printDataError(err.errors);
                } else {
                    printErrorMsgNew($("#import-form"), err.errors);
                }
            } else if(typeof xhr != 'undefined' && xhr.status == 500) {
                errorHandler(err.message);
            } else {
                errorHandler(err);
            }
        });

        insDropzone.on('complete', function(file) {
            this.removeAllFiles();
        });

        insDropzone.on("addedfile", function(file) {
            $('.preview-container').css('visibility', 'visible');
            file.previewElement.classList.add('type-' + fileType(file.name)); // Add type class for this element's preview
            $(".help-block").html('');
        });

        insDropzone.on("totaluploadprogress", function (progress) {
            var progr = document.querySelector(".progress .determinate");
            if (progr === undefined || progr === null) return;
            progr.style.width = progress + "%";
        });

        insDropzone.on('dragenter', function () {
            $(target).addClass("hover");
        });

        insDropzone.on('dragleave', function () {
            $(target).removeClass("hover");
        });

        insDropzone.on('drop', function () {
            $(target).removeClass("hover");
        });

        insDropzone.on('addedfile', function () {
            $(".no-files-uploaded").slideUp("easeInExpo");
            $(".help-block").html('');
                            
        });

        insDropzone.on('removedfile', function (file) {
            // Show no files notice
            if ( dropzoneCount() == 0 ) {
                $(".no-files-uploaded").slideDown("easeInExpo");
                $(".uploaded-files-count").html(dropzoneCount());
                // errorDropzone();
                // console.log('{{old("type")}}');
                // if ('{{old("type")}}' != '') {
                //     alert();
                //     $(".help-block").html('');
                // }
            }
        });

        insDropzone.on("maxfilesexceeded", function(file) {
            this.removeAllFiles();
            this.addFile(file);
        });

        function successHandler(res='') {
            $(".help-block").remove('');
            $("#upload-btn").prop("disabled", false);
            printAjaxSuccessMsg(res.message);
                $(".alert-warning").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            $(window).scrollTop(0);
        }

        function errorHandler(xhr) {
            $("#upload-btn").prop("disabled", false);
            $(window).scrollTop( $(".container").offset().top );
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                printErrorMsgNew($("#import-form"), xhr.responseJSON.errors);
            } else {
                printAjaxErrorMsg(xhr);
            }
            $(".alert-warning").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
        }

        function printDataError(errors) {
            $('html,body').animate({
                scrollTop:  $(".messageErr").offset().top
            }, "slow");
            @if($upload_id == 1)
             title = "Bulk Upload Failed";
            @else
                title = 'Single Script Upload Failed';
            @endif
            var message = '<p style="color:red; font-weight:bold;"> '+title+'</p>';
            $.each(errors, function (key, error) {
                message += "<p>" +error+ "</p>";
            });
            var data = '<div class="alert alert-warning" id ="validations-array" style = "background-color: #f2dede;border-color:#f2dede;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div class="scroller scripts-alt-w">'+message+ '</div></div>';
            $(".messageErr").html(data);
        }
        function errorDropzone()
        {
            $('.help-block').text('This field is required');
            // $("#import-form").find("[name='csv']").after("<span class='help-block' >This field is required</span>");
        }

    </script>
@endpush
