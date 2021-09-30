<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='leads-by-sclocation-table-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Lead Status by Sales Center Locations</h4>
    <div style="width: 100%; height: 220px;">
        <table class = "table " id="dashboard-salescenter-location-lead-status" style="width: 100%;">
            <thead class = "">
            <tr>
                <th class="dashboard-table-color">Sales Center Location</th>
                <th class="dashboard-table-color">Bad Sales</th>
                <th class="dashboard-table-color">Cancelled Leads</th>
                <th class="dashboard-table-color">Good Sales</th>
                <th class="dashboard-table-color">Pending Leads</th>
                <th class="dashboard-table-color">Total</th>
            </tr>
            </thead>
            <tbody id = "salescenter-location-leads scroller">
            </tbody>
        </table>
    </div>
    
</div>
@push('scripts')
    <script>
        function loadLeadStatusBySCLocation(data)
        {
            $.ajax({
                url: '{{route("dashboard.leads-status-table-by-sclocations")}}',
                method:'post',
                data:data,
                success:function(data)
                {   
                    if(data.status == 'success') {
                        $('#leads-by-sclocation-table-loading').css('visibility','hidden');
                        // console.log(data.data.data);
                        locations = data.data.data;
                        tableData = [];
                        i=0;
                        goodTotal = 0;
                        badTotal = 0;
                        pendingTotal = 0;
                        cancelTotal = 0;
                        grantTotal = 0;
                        $.each(locations,function(k,v){
                            goodTotal += v['good_sale'];
                            badTotal += v['bad_sale'];
                            pendingTotal += v['pending_leads'];
                            cancelTotal += v['cancelled_leads'];
                            grantTotal += v['total_leads'];
                            tableData[i++] = [v['name'],v['bad_sale'],v['cancelled_leads'],v['good_sale'],v['pending_leads'],v['total_leads']];
                        });
                        tableData[i] = ['Total',badTotal,cancelTotal,goodTotal,pendingTotal,grantTotal];
                        loadTableData(tableData);
                        // $('#salescenter-location-leads').html(data.data);
                    }
                    if(data.status == 'error'){
                         console.log(data)
                    }
                }
            });
        }

        function loadTableData(tableData)
        {
            let table = $('#dashboard-salescenter-location-lead-status');
            $('#dashboard-salescenter-location-lead-status').DataTable({
                retrieve: true,
                paging: false,
                info: false,
                searching: false,                   
            });

            table.dataTable().fnClearTable();
            table.dataTable().fnAddData(tableData);
   
        }
    </script>
@endpush