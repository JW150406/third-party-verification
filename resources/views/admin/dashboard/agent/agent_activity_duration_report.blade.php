<div class="dashboard-box" id="agent_activity_duration_report_div_id">
	<span class="dashboard-spiner-icon" id="twilio-task-progress-loading">
		<i class="fas fa-circle-notch fa-spin" aria-hidden="true "></i>
    </span>
    <h4 class="dash-hd-title">Agent Activity Duration Report</h4>
    <div class="row">
        <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0">            
                <div class="btn-group pull-right btn-sales-all">
                
                    <div class="update_client_by_location" style="padding-top: 10px;">
                            <a href="javascript:void(0)" id="agent-reset-filter"  type="button"  class="mr0" style="border-bottom: 1px solid #1c5997;">Reset Filter</a>
                    </div>
                
                </div>

            <div class="sor_fil utility-btn-group mr15">
                    <button type="button" class="form-control" onclick='refreshAgentReport();'> <i class="prev-nex-btn fa fa-refresh fa-3x" aria-hidden="true"></i> </button>
            </div>

            <div class="sor_fil utility-btn-group">
                <div class="search mr15">
                    <div class="search-container margin-bottom-for-filters" style="width: 130px">
                        <button type="button">{!! getimage('images/search.png') !!}</button>
                        <input placeholder="Search..." id="search_agent_duration_report_name" type="text" value="" name="searchText">
                    </div>
                </div>
            </div>

            <div class="sor_fil utility-btn-group mr15">
                    <button type="button" id="agent_next-week-btn" class="form-control" onclick='agentNextOneWeek();'> <i class="prev-nex-btn fa fa-angle-right fa-3x" aria-hidden="true"></i> </button>
            </div>

            <div class="sor_fil utility-btn-group mr15">
                    <button type="button" class="form-control" onclick='agentPreviousOneWeek();'> <i class="prev-nex-btn fa fa-angle-left fa-3x" aria-hidden="true"></i> </button>
            </div>

            {{-- Date range filter --}}
            <div class="sor_fil utility-btn-group mr15 margin-bottom-for-filters">
                <div class="search">
                    <div class="search-container date-search-container { $errors->has('duration_date_start') ? ' has-error' : '' }}">
                        <button for="duration_date_start" type="button">{!! getimage('images/calender.png') !!}</button>
                        <input id="duration_date_start" autocomplete="off" type="text" class="form-control" name="duration_date_start">
                    </div>
                </div>
            </div>

        </div>
        <!--end--col-12------->
    </div>
    
    <div style="width: 100%; min-height: 330px;">    
        <p style="float: right;
        margin-right: 20px;
        font-style: italic;
        color: #000;">
        <span style="color:red;">* </span>This data is delayed up to 15 minutes</p>        
        <table class="table " id="agent-activity-duration-report" style="width: 100%;"></table>
    </div>
</div>
@push('scripts')
<script>
    var today, firstDay;
    $(document).ready(function() {        
        today = new Date();
        usaTime = today.toLocaleString("en-US", {
            timeZone: "{{Auth::user()->timezone}}"
        });
        today = new Date(usaTime);
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        firstDay = moment().startOf('isoWeek');
        today = moment().endOf('isoWeek');

        $('#duration_date_start').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
            maxDate: new Date(),
        });


        agentResetFilterDate(firstDay, today);
        agentEnableDisableNextWeekBtn();

        var durationTable = $('#agent-activity-duration-report').DataTable({
            dom: 'Rtr<"bottom"lip>',
            processing: true,
            serverSide: true,
            lengthChange: true,
            searchDelay: 1000,
            ordering: true,
            searching: true,
            // 'scrollX':true,
            scroller: true,
            colReorder: {
                allowReorder: false
            },
            pageLength: 10,
            ajax: {
                url: "{{ route('reports.agent-activity-duration-report') }}",
                method: "get",
                data: function(d) {
                    d._token = '{{csrf_token()}}';
                    d.submitDate = $('#duration_date_start').val();
                    d.searchText = $('#search_agent_duration_report_name').val();
                }
            },

            aaSorting: [[0, 'asc']],
            columns: [{
                    data: 'TPVAgent',
                    title: 'TPV Agent',
                    searchable: true,
                    orderable: true,
                    width:'20%',
                    className: 'get-agent-activity',
                    
                },
                {
                    data: 'onlineDuration',
                    title: 'Available',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'unavailableDuration',
                    title: 'Unavailable  (On Call)',
                    searchable: true,
                    width:'30%',
                },
                {
                    data: 'wrapUpDuration',
                    title: 'Wrap Up',
                    searchable: true,
                    width:'20%',
                },                
                {
                    data: 'offlineDuration',
                    title: 'Offline',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'breakDuration',
                    title: 'Break',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'lunchDuration',
                    title: 'Lunch',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'meetingDuration',
                    title: 'Meeting',
                    searchable: true,
                    width:'20%',
                },                
                {
                    data: 'trainingDuration',
                    title: 'Training',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'technicalDifficultyDuration',
                    title: 'Technical Difficulty',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'otherDuration',
                    title: 'Other',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'coachingDuration',
                    title: 'Coaching',
                    searchable: true,
                    width:'20%',
                }                
            ],
            'fnDrawCallback': function() {
                var table = $('#agent-activity-duration-report').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#agent-activity-duration-report_info')[0].style.display = 'block';
                    $('#agent-activity-duration-report_paginate')[0].style.display = 'block';
                } else {
                    $('#agent-activity-duration-report_info')[0].style.display = 'none';
                    $('#agent-activity-duration-report_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#agent-activity-duration-report_length')[0].style.display = 'none';
                } else {
                    $('#agent-activity-duration-report_length')[0].style.display = 'block';
                }

            }
        });

        $('#duration_date_start').on('apply.daterangepicker', function(ev, picker) {
            durationTable.ajax.reload();
        });

        $('#duration_date_start').on('cancel.daterangepicker', function() {
            agentResetFilterDate(firstDay, today);
            durationTable.ajax.reload();
        });
        

        $('.toggleColumns').on('click', function(e) {
            if ($(this).attr('type') == 'plus') {
                $(this).attr('type', 'minus');
                $(this).html('<i class="fa fa-minus-circle"></i>');
            } else {
                $(this).attr('type', 'plus');
                $(this).html('<i class="fa fa-plus-circle"></i>');
            }
            var table = $('#agent-activity-duration-report').DataTable();

            e.preventDefault();
            // Get the column API object
            for (i = 13; i < table.columns().header().length; i++) {
                var column = table.column(i);
                column.visible(!column.visible());
            }

            // Toggle the visibility
        });

        // Refresh data on time interval
        // setInterval(function(){            
        //     durationTable.ajax.reload();
        // }, "{{ config('constants.TPV_AGENTS_DASHBOARD_AUTO_REFRESH_INTERVAL') }}");

        $('#search_agent_duration_report_name').on('change',function(e){
            $("#agent-reset-filter").show();
            durationTable.ajax.reload();
        });

        $('#agent-reset-filter').hide();

    });

    function agentResetFilterDate(startDate,endDate) 
    {
        $('#duration_date_start').data('daterangepicker').setStartDate(startDate);
        $('#duration_date_start').data('daterangepicker').setEndDate(endDate); 
    }

    function agentPreviousOneWeek(){


        // get current date and extract
        var selectedRange = $('#duration_date_start').val();
        var chars = selectedRange.split(' - ');

        // Start of week

        var dt = new Date(chars[0]);
        dt.setDate(dt.getDate()-7);

        var dd = String(dt.getDate()).padStart(2, '0');
        var mm = String(dt.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = dt.getFullYear();

        var startWeekDay = mm + '/' + dd + '/' + yyyy;

        // End of week
        var endDt = new Date(startWeekDay);
        endDt.setDate(endDt.getDate()+6);

        var dd = String(endDt.getDate()).padStart(2, '0');
        var mm = String(endDt.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = endDt.getFullYear();

        var endWeekDay = mm + '/' + dd + '/' + yyyy;

        // $('#duration_date_start').daterangepicker({
        //     autoUpdateInput: true,
        //     startDate: startWeekDay,
        //     endDate: endWeekDay,
        //     maxDate: endWeekDay
        // });
        agentResetFilterDate(startWeekDay,endWeekDay);
        agentEnableDisableNextWeekBtn();
        $('#agent-activity-duration-report').DataTable().ajax.reload();
    }

    function agentNextOneWeek(){

        // get current date and extract
        var selectedRange = $('#duration_date_start').val();
        var chars = selectedRange.split(' - ');

        // Start of week

        var dt = new Date(chars[1]);
        dt.setDate(dt.getDate()+1);

        var dd = String(dt.getDate()).padStart(2, '0');
        var mm = String(dt.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = dt.getFullYear();

        var startWeekDay = mm + '/' + dd + '/' + yyyy;

        // End of week
        var endDt = new Date(startWeekDay);
        endDt.setDate(endDt.getDate()+6);

        var dd = String(endDt.getDate()).padStart(2, '0');
        var mm = String(endDt.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = endDt.getFullYear();

        var endWeekDay = mm + '/' + dd + '/' + yyyy;

        // $('#duration_date_start').daterangepicker({
        //     autoUpdateInput: true,
        //     startDate: startWeekDay,
        //     endDate: endWeekDay,
        //     // maxDate: endWeekDay
        // });

        agentResetFilterDate(startWeekDay,endWeekDay);
        agentEnableDisableNextWeekBtn();

        $('#agent-activity-duration-report').DataTable().ajax.reload();
    }

    function agentEnableDisableNextWeekBtn(){
        // get current date and extract
        var selectedRange = $('#duration_date_start').val();
        var chars = selectedRange.split(' - ');

        // Start of week

        var dt = new Date(chars[0]);

        var dd = String(dt.getDate()).padStart(2, '0');
        var mm = String(dt.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = dt.getFullYear();

        var startWeekDay = mm + '/' + dd + '/' + yyyy;

        // End of week
        var endDt = new Date(chars[1]);

        var dd = String(endDt.getDate()).padStart(2, '0');
        var mm = String(endDt.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = endDt.getFullYear();

        var endWeekDay = mm + '/' + dd + '/' + yyyy;
        console.log(startWeekDay, endWeekDay);

        var d = new Date();

        var month = d.getMonth()+1;
        var day = d.getDate();

        var output = (month<10 ? '0' : '') + month + '/' + (day<10 ? '0' : '') + day +'/'+d.getFullYear();
        console.log(output);

        if(output >= startWeekDay && output <= endWeekDay){
            $('#agent_next-week-btn').attr('disabled','disabled');
        }else{
            $('#agent_next-week-btn').removeAttr('disabled');
        }

    }

    function refreshAgentReport(){
        $("#agent-reset-filter").hide();
        $('#agent-activity-duration-report').DataTable().ajax.reload();
    }

    $("#agent-reset-filter").click(function() {
        today = new Date();
        usaTime = today.toLocaleString("en-US", {
            timeZone: "{{Auth::user()->timezone}}"
        });
        today = new Date(usaTime);
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        firstDay = moment().startOf('isoWeek');
        today = moment().endOf('isoWeek');

        $('#duration_date_start').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
        });

        $('#search_agent_duration_report_name').val('');


        agentResetFilterDate(firstDay, today);
        agentEnableDisableNextWeekBtn();

        $("#agent-reset-filter").hide();
        $('#agent-activity-duration-report').DataTable().ajax.reload();
    });

    $("#agent_activity_duration_report_div_id :input,select").change(function() {
        $("#agent-reset-filter").show();
    });


</script>

@endpush

