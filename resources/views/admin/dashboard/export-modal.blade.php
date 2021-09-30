<div class="modal fade status-lead-modal" id="telesales-status-leads-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="false" style="z-index:999999999999;">
    <div class="modal-dialog" role="document" style="width: 82%">
        <div class="modal-content font-12">
            <div class="modal-header">
                <button type="button" class="close export-close" data-dismiss="modal"  aria-label="Close"><span aria-hidden="true">×</span></button>
                <a href="javascript:void(0)" id="exportLeads" class="btn btn-green pull-right" data-type="new" type="button">Export</a>
                <h4 class="modal-title">Leads Report</h4>
            </div>
            <div class="ajax-error-message"></div>
            <div class="modal-body dash-m-scroll ">
                <div class="scrollbar-inner">
                    <div class="modal-form">
                        <div class="col-xs-12 col-sm-12 col-md-12" style="padding:0px;">
                            <div class="sales_tablebx dash-lead-report ft1">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="telesales-status-leads">
                                        <thead>
                                        <tr class="list-users">
                                            <th>Sr.No.</th>
                                            <th>Lead#</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Sales Agent</th>
                                            <th>Sales Center</th>
                                            <th>Sales Center Location</th>
                                            <th>State</th>
                                            <th>Channel</th>
                                            <th>Commodity</th>
                                            <!-- <th>Zipcode</th> -->
                                            <th>TPV Agent</th>
                                            <th>Verification Method</th>
                                            <!-- <th style="display: none;">Verification Method</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="telesalesstatusleadsreporttable"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

<script>

$("#telesales-status-leads-modal").on('hide.bs.modal', function() {
            isExportModalOpen = false;
            // $('#telesales-status-leads').DataTable().clear();
            $('#telesales-status-leads').dataTable().fnClearTable();
            $('.charthiddenfield #status').val('');
            $('.charthiddenfield #sheet_name').val('');
            $('.charthiddenfield #sheet_title').val('');
            $('.charthiddenfield #agent_id').val('');
            $('.charthiddenfield #sales_center_id').val('');
            $('.charthiddenfield #channelType').val('');
            $('.charthiddenfield #commodity_type').val('');
            $('.charthiddenfield #verificationMethod').val('');
            $('.charthiddenfield #sales_type').val('');
            $('.charthiddenfield #agent_type').val('');
            $('.charthiddenfield #calender_day').val('');
            $('.charthiddenfield #locationCommodity').val('');
            $('.charthiddenfield #program_id').val('');
            $('.charthiddenfield #utility_name').val('');
            $('.charthiddenfield #state').val('');
            $('.hidden-sales-location-id').attr('value', '');
            $("#telesales-status-leads").dataTable().fnDestroy();
        });

function getTelesalesLeadsByStatus(status, agentId, salesCenterId, channelType, commodity_type, verificaitonMethod, sales_type,locationId,calenderDay,month,year,locationCommodity=false,programId="",utilityName="",state="") {
    // console.log(brand);
        $('#telesales-status-leads-modal').modal();
        var filterData = $("#deshbordNewForm").serializeArray();
        
        var leadTable = $('#telesales-status-leads').DataTable({
            dom: 'Rtr<"bottom"lip>',
            // dom: 'Bfrtip',
            // dom: 'Rlrtip',
            colReorder: {
                allowReorder: false
            },
            processing: true,
            serverSide: true,
            bDestroy: true,
            searchDelay: 1000,
            autoWidth: true,
            lengthChange: true,
            ajax: {
                url: "{{ route('admin.dashboard.telesalesleadslist') }}",
                data: function(data) {
                    data.client_id = filterData[1].value;
                    data.start_date = filterData[2].value;
                    data.end_date = filterData[3].value;
                    data.brand = filterData[4].value;
                    data.status = status;
                    data.agent_id = agentId;
                    data.sales_center_id = salesCenterId;
                    data.channelType = channelType;
                    data.commoditytype = commodity_type;
                    data.verificaitonMethod = verificaitonMethod;
                    data.sales_type = sales_type;
                    data.locationId = locationId;
                    data.calenderDay = calenderDay;
                    data.month = month;
                    data.year = year;
                    data.locationCommodity = locationCommodity;
                    data.programId = programId;
                    data.utilityName = utilityName;
                    data.state = state;
                    
                }
                // data:filterData
            },

            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'refrence_id',
                    name: 'telesales.refrence_id',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'telesales.status',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'telesales.created_at',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'sales_agent',
                    name: 'users.first_name',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'salescenter_name',
                    name: 'salescenters.name',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'salescenter_location',
                    name: 'salescenterslocations.name',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'state',
                    name: 'zip_codes.state',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'channel',
                    name: 'salesagent_detail.agent_type',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'commodity_name',
                    name: 'commodities.name',
                    orderable: true,
                    searchable: false
                },  
                // {
                //     data: 'zipcode',
                //     name: 'telesales.service_zipcode',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'tpv_agent',
                    name: 'users.first_name',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'verification_method',
                    name: 'telesales.verification_method',
                    orderable: true,
                    searchable: false
                },
                // {
                //     data: 'id',
                //     name: 'telesales.id',
                //     searchable: false,
                //     visible: false
                // },
            ],
            //buttons: ['csv'],
            columnDefs: [{
                    "searchable": false,
                    "orderable": false,
                    "width": "5%",
                    "targets": 0
                }
                // {
                //     "visible": false,
                //     "targets": 11
                // }
            ],
            'fnDrawCallback': function() {
                var table = $('#telesales-status-leads').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#telesales-status-leads_info')[0].style.display = 'block';
                    $('#telesales-status-leads_paginate')[0].style.display = 'block';
                } else {
                    $('#telesales-status-leads_info')[0].style.display = 'none';
                    $('#telesales-status-leads_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#telesales-status-leads_length')[0].style.display = 'none';
                } else {
                    $('#telesales-status-leads_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#telesales-status-leads').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        });
    }

        $("#exportLeads").on('click', function() {
            var data = $("#deshbordNewForm").serialize();
            var start_date = moment(data[3].value).format('DD-MM-YYYY');
            var end_date = moment(data[4].value).format('DD-MM-YYYY');
            var url = "{{ route('admin.dashboard.export.verificationstatusreport', ['data']) }}";
            urlcleintid = url.replace('data', '?' + data);
            window.open(urlcleintid, '_blank');
        });

    
</script>
@endpush