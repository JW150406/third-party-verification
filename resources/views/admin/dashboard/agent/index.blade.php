@extends('layouts.admin')
@section('content')

<div class="dashboard-bg daseboard-update">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12" id="dashboard-warp">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6">
                    @include('admin.dashboard.agent.agents_details')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    @include('admin.dashboard.agent.agent_avail_report_client_wise')
                </div>
                
			</div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    @include('admin.dashboard.agent.twilio_task_status')
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    @include('admin.dashboard.agent.agent_dashboard_report')
                </div>
			</div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    @include('admin.dashboard.agent.agent_activity_duration_report')
                </div>
			</div>
        </div>
    </div>
</div>

<div class="modal fade status-lead-modal agent-activity-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 50%">
        <div class="modal-content font-12">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <a href="javascript:void(0)" id="exportActivityReport" class="btn btn-green pull-right" data-type="new" type="button">Export</a>
                <h4 class="modal-title">Agent Activity Report</h4>
            </div>
            <div class="ajax-error-message"></div>
            <div class="modal-body dash-m-scroll">
                <div class="scrollbar-inner">
                    <div class="modal-form">
                        <input type="hidden" id="agent_id">
                        <div class="col-sm-6 col-md-6">
                            
                            <div class="btn-group btn-sales-all">
                                <div class="update_client_by_location" style="margin-left:15px">
                                    <h4 id="agent-assigned-client"></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="sor_fil utility-btn-group mr15 margin-bottom-for-filters">
                                <div class="search">
                                    <div class="search-container date-search-container">
                                        <button for="duration_date_start" type="button">{!! getimage('images/calender.png') !!}</button>
                                        <input id="activity_duration_date_start" autocomplete="off" type="text" class="form-control" name="activity_duration_date_start">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="sales_tablebx dash-lead-report ft1">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="agent-activity-modal">
                                        <thead>
                                        <tr class="list-users">
                                            <th>Time (EST)</th>
                                            <th>Activity</th>
                                            <th>Duration</th>
                                        </tr>
                                        </thead>
                                        <tbody id="agentactivitesreporttable" class="scroller"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
		setInterval(function() {
            if(!document.hidden){
                console.log('visible');
                // getAgentDetails();
                $('#agent-avail-report-client-update').DataTable().ajax.reload();
			    $('#twilio-task-progress-update').DataTable().ajax.reload();
            }else{
                console.log('hidden');
            }
			
		}, {{ config()->get('constants.TPV_AGENTS_DASHBOARD_AUTO_REFRESH_INTERVAL') }});

        jQuery(document).ready(function(){
            jQuery(document).on("click","td.get-agent-activity", function(){
                var _this = jQuery(this);
                jQuery("#agent_id").val(jQuery(_this).parent('tr').attr('id'));
                jQuery(".modal-title").html(jQuery(_this).parent('tr').data('name'));
                jQuery("#agent-assigned-client").html("<strong>Assigned Clients: </strong>"+jQuery(_this).parent('tr').data('client'));
                // get current date and extract
                var selectedRange = $('#duration_date_start').val();
                var chars = selectedRange.split(' - ');
                var startDate = new Date(chars[0]);
                var endDate = new Date(chars[1]);
                $('#activity_duration_date_start').daterangepicker({
                    autoUpdateInput: true,
                    startDate: startDate,
                    endDate: endDate,
                    maxDate: new Date(),
                });

                $('#agent-activity-modal').DataTable().destroy();
                jQuery(".agent-activity-modal").modal('show');
                var activityTable = $('#agent-activity-modal').DataTable({
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
                        url: "{!! route('agent.dashboard.agent-activity-report') !!}",
                        method: "get",
                        data: function(d) {
                            d._token = '{{csrf_token()}}';
                            d.id = jQuery("#agent_id").val();
                            d.submitDate = $('#activity_duration_date_start').val();
                        }
                    },

                    aaSorting: [[0, 'asc']],
                    columns: [{
                            data: 'created_at',
                            title: 'Time (EST)',
                            searchable: true,
                            orderable: true,
                            width:'40%',
                        },
                        {
                            data: 'worker_activity_name',
                            title: 'Activity',
                            searchable: true,
                            width:'30%',
                        },
                        {
                            data: 'duration',
                            title: 'Duration',
                            searchable: true,
                            width:'30%',
                        }              
                    ],
                    'fnDrawCallback': function() {
                        var table = $('#agent-activity-modal').DataTable();
                        var info = table.page.info();
                        if (info.pages > 1) {
                            $('#agent-activity-modal_info')[0].style.display = 'block';
                            $('#agent-activity-modal_paginate')[0].style.display = 'block';
                        } else {
                            $('#agent-activity-modal_info')[0].style.display = 'none';
                            $('#agent-activity-modal_paginate')[0].style.display = 'none';
                        }
                        if (info.recordsTotal < 10) {
                            $('#agent-activity-modal_length')[0].style.display = 'none';
                        } else {
                            $('#agent-activity-modal_length')[0].style.display = 'block';
                        }

                    }
                });

                $(document).on('click',"#exportActivityReport",function() {
                    var data = {
                        id: jQuery("#agent_id").val(),
                        submitDate: $('#duration_date_start').val()
                    };
                    data = jQuery.param( data );
                    var url = "{{ route('admin.dashboard.export.agentactivityreport', ['data']) }}";
                    urlcleintid = url.replace('data', '?' + data);
                    window.open(urlcleintid, '_blank');
                });

                $('#activity_duration_date_start').on('apply.daterangepicker', function(ev, picker) {
                    activityTable.ajax.reload();
                });

                $('#activity_duration_date_start').on('cancel.daterangepicker', function() {
                    agentResetFilterDate(startDate, endDate);
                    activityTable.ajax.reload();
                });
            });
        });
        
    </script>
@endpush