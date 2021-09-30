<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="cont_bx3">
            <div class="tpvbtn">
                <div class="col-md-12">
                    <div class="cont_bx3 mt30 sor_fil">
                        @if($salescenter->isActive() && $salescenter->isActiveClient())
                        <div class="btn-group pull-right">
                            @if(auth()->user()->hasPermissionTo('add-sales-agents'))
                                <a href="javascript:void(0)" class="btn btn-green mr15 salesagent-modal" type="button"
                                   data-type="new" data-title="Add Sales Agent">Add Sales Agent</a>
                            @endif
                            <button type="button" class="btn btn-green dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                More <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu employee-dropdown" role="menu">
                                @if(auth()->user()->hasPermissionTo('add-sales-agents'))
                                    <li>
                                        <a href="{{ route('client.salesagents.bulkupload', array($client_id, $salecenter_id)) }}" type="button">Bulk Upload</a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ route('client.salesagents.exportAgents', array($client_id, $salecenter_id)) }}" type="button">Export</a>
                                </li>
                            </ul>
                        </div>
                        @endif
                        <div class="btn-group pull-right btn-sales-all">
                            <select name="filtter_active_inactive" id="active_inactive"
                                    class="select2 btn btn-green dropdown-toggle mr15 active_inactive_data" role="menu">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="agent-table">
                        <thead>
                        <tr class="heading acjin">
                            <th>Sr. No.</th>
                            <th></th>
                            <th>Id</th>
                            <th>External Id</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th class="action-width" style="min-width:97px;">Action</th>
                        </tr>
                        </thead>

                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#agent-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: true,
            dom: 'tr<"bottom"lip>',
            ajax: {
                url: "{{ route('client.salescenter.salesagents', array($client_id, $salecenter_id)) }}",
                data: function (d) {
                    d.status = $('select#active_inactive option:selected').val()
                }
            },
            /*ajax: "{{ route('client.salescenter.salesagents', array($client_id, $salecenter_id)) }}",*/
            aaSorting: [
                [8, 'desc']
            ],
            columns: [{
                data: null
                },
                {
                    data: 'profile_picture',
                    orderable:false,
                    searchable:false
                },
                {
                    data: 'userid',
                    name: 'userid'
                },
                {
                    data: 'external_id',
                    name: 'salesAgentDetails.external_id'
                },
                {
                    data: 'full_name',
                    name: 'first_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'agent_type',
                    name: 'salesAgentDetails.agent_type'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
                {data: 'id',searchable:false,visible: false},
                {data: 'last_name',name:'last_name',visible: false,orderable: false},
            ],
            columnDefs: [{
                "searchable": false,
                "orderable": false,
                "width": "5%",
                "targets": 0
            }],
            'fnDrawCallback': function () {
                var table = $('#agent-table').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#agent-table_info')[0].style.display = 'block';
                    $('#agent-table_paginate')[0].style.display = 'block';
                } else {
                    $('#agent-table_info')[0].style.display = 'none';
                    $('#agent-table_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#agent-table_length')[0].style.display = 'none';
                } else {
                    $('#agent-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                var table = $('#agent-table').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        }).on( 'processing.dt', function ( e, settings, processing ) {
            $(".tooltip").tooltip("hide");
        });

        $(".active_inactive_data").change(function () {

            $("#agent-table").DataTable().ajax.reload();
        });

        /*$('.active_inactive_data').onchange(function() {
            $("#agent-table").DataTable().ajax.reload();
        })*/
    });
</script>
@include('client.salescenter.salesagent.salesagentspoup_new')
@include('client.salescenter.salesagent.addsalesagentpopup')
