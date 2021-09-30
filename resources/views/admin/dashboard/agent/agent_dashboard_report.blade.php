<div class="dashboard-box" id="agent_dashboard_report_div_id">
	<span class="dashboard-spiner-icon" id="twilio-task-progress-loading">
		<i class="fas fa-circle-notch fa-spin" aria-hidden="true "></i>
    </span>
    <h4 class="dash-hd-title">Agent Report</h4>
    <div class="row">
        <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0">            
            @include('reports.filters.reset')

            <div class="sor_fil utility-btn-group mr15">
                    <button type="button" class="form-control" onclick='refreshAgentDashboardReport();'> <i class="prev-nex-btn fa fa-refresh fa-3x" aria-hidden="true"></i> </button>
            </div>

            <div class="sor_fil utility-btn-group">
                <div class="search mr15">
                    <div class="search-container margin-bottom-for-filters" style="width: 130px">
                        <button type="button">{!! getimage('images/search.png') !!}</button>
                        <input placeholder="Search..." id="search_agent_report_name" type="text" value="" name="searchText">
                    </div>
                </div>
            </div>

            <div class="sor_fil utility-btn-group mr15">
                    <button type="button" id="next-week-btn" class="form-control" onclick='nextOneWeek();'> <i class="prev-nex-btn fa fa-angle-right fa-3x" aria-hidden="true"></i> </button>
            </div>

            <div class="sor_fil utility-btn-group mr15">
                    <button type="button" class="form-control" onclick='previousOneWeek();'> <i class="prev-nex-btn fa fa-angle-left fa-3x" aria-hidden="true"></i> </button>
            </div>

            {{-- Date range filter --}}
            <div class="sor_fil utility-btn-group mr15 margin-bottom-for-filters">
                <div class="search">
                    <div class="search-container date-search-container { $errors->has('date_start') ? ' has-error' : '' }}">
                        <button for="date_start" type="button">{!! getimage('images/calender.png') !!}</button>
                        <input id="date_start" autocomplete="off" type="text" class="form-control" name="date_start">
                    </div>
                </div>
            </div>

        </div>
        <!--end--col-12------->
    </div>
    
    <div style="width: 100%; min-height: 330px;">            
        <table class="table " id="agent-report" style="width: 100%;"></table>
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

        $('#date_start').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
            maxDate: new Date(),
        });


        resetFilterDate(firstDay, today);
        enableDisableNextWeekBtn();

        var enrollmentTable = $('#agent-report').DataTable({
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
                url: "{{ route('reports.agent-report-data') }}",
                method: "get",
                data: function(d) {
                    d._token = '{{csrf_token()}}';
                    d.submitDate = $('#date_start').val();
                    d.searchText = $('#search_agent_report_name').val();
                }
            },

            // aaSorting: [[23, 'desc']],
            columns: [{
                    data: 'TPVAgent',
                    title: 'TPV Agent',
                    searchable: true,
                    width:'20%',                    
                },
                {
                    data: 'created_at',
                    title: 'Date Of Joining',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'totalCalls',
                    title: 'Total Calls',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'declinedCalls',
                    title: 'Declined Calls',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'disconnectedCalls',
                    title: 'Disconnected Calls',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'pendingCalls',
                    title: 'Pending Calls',
                    searchable: true,
                },
                {
                    data: 'expiredCalls',
                    title: 'Expired Calls',                    
                },
                // {
                //     data: 'nullCalls',
                //     title: 'Null Calls',
                //     searchable: true,
                //     width:'20%',
                // },
                {
                    data: 'verifiedCalls',
                    title: 'Verified Calls',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'verifiedCallsPercentage',
                    title: '% Verified',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'acceptedCalls',
                    title: 'Accepted Calls',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'rejectedCalls',
                    title: 'Rejected Calls',
                    searchable: true,
                    width:'20%',
                },
                {
                    data: 'timeoutCalls',
                    title: 'Timeout Calls',
                    searchable: true,
                    width:'20%',
                },               
            ],
            'fnDrawCallback': function() {
                var table = $('#agent-report').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#agent-report_info')[0].style.display = 'block';
                    $('#agent-report_paginate')[0].style.display = 'block';
                } else {
                    $('#agent-report_info')[0].style.display = 'none';
                    $('#agent-report_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#agent-report_length')[0].style.display = 'none';
                } else {
                    $('#agent-report_length')[0].style.display = 'block';
                }

            }
        });

        $('#date_start').on('apply.daterangepicker', function(ev, picker) {
            enrollmentTable.ajax.reload();
        });

        $('#date_start').on('cancel.daterangepicker', function() {
            resetFilterDate(firstDay, today);
            enrollmentTable.ajax.reload();
        });
        

        $('.toggleColumns').on('click', function(e) {
            if ($(this).attr('type') == 'plus') {
                $(this).attr('type', 'minus');
                $(this).html('<i class="fa fa-minus-circle"></i>');
            } else {
                $(this).attr('type', 'plus');
                $(this).html('<i class="fa fa-plus-circle"></i>');
            }
            var table = $('#agent-report').DataTable();

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
        //     enrollmentTable.ajax.reload();
        // }, "{{ config('constants.TPV_AGENTS_DASHBOARD_AUTO_REFRESH_INTERVAL') }}");

        $('#search_agent_report_name').on('input',function(e){
            enrollmentTable.ajax.reload();
            $("#reset-filter").show();
        });
        
        $("#reset-filter").hide();
        
    });

    function previousOneWeek(){


        // get current date and extract
        var selectedRange = $('#date_start').val();
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

        // $('#date_start').daterangepicker({
        //     autoUpdateInput: true,
        //     startDate: startWeekDay,
        //     endDate: endWeekDay,
        //     maxDate: endWeekDay
        // });
        resetFilterDate(startWeekDay,endWeekDay);
        enableDisableNextWeekBtn();
        $('#agent-report').DataTable().ajax.reload();
    }

    function nextOneWeek(){

        // get current date and extract
        var selectedRange = $('#date_start').val();
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

        // $('#date_start').daterangepicker({
        //     autoUpdateInput: true,
        //     startDate: startWeekDay,
        //     endDate: endWeekDay,
        //     // maxDate: endWeekDay
        // });

        resetFilterDate(startWeekDay,endWeekDay);
        enableDisableNextWeekBtn();

        $('#agent-report').DataTable().ajax.reload();
    }

    function enableDisableNextWeekBtn(){
        // get current date and extract
        var selectedRange = $('#date_start').val();
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
            $('#next-week-btn').attr('disabled','disabled');
        }else{
            $('#next-week-btn').removeAttr('disabled');
        }

    }

    function refreshAgentDashboardReport(){
        $("#reset-filter").hide();
        $('#agent-report').DataTable().ajax.reload();
    }

    $("#reset-filter").click(function() {
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

        $('#date_start').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
        });

        $('#search_agent_report_name').val('');


        resetFilterDate(firstDay, today);
        enableDisableNextWeekBtn();

        $("#reset-filter").hide();
        $('#agent-report').DataTable().ajax.reload();
    });

    $("#agent_dashboard_report_div_id :input,select").change(function() {
        $("#reset-filter").show();
    });

    function resetFilterDate(startDate,endDate) 
    {
        $('#date_start,#filter_date,#submission_date').data('daterangepicker').setStartDate(startDate);
        $('#date_start,#filter_date,#submission_date').data('daterangepicker').setEndDate(endDate); 
    }

</script>
@endpush

