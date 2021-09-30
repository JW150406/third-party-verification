<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='leads-by-state-table-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads By State </h4>
    <div style="width: 100%;" class="dashboard-state-lead-div">
         <table class = "table" id="status-by-rate-table" style="width: 100%;">
            <thead>
                <tr>
                <th style='background-color:#3A58A8; color: white ; position:sticky;top:0;' style="display:none;">Id</th>
                <th style='background-color:#3A58A8; color: white ; position:sticky;top:0;'>State</th>
                    @if($sId == '')
                        <th style='background-color:#3A58A8; color: white ; position:sticky;top:0;'>Sales Center Location</th>
                    @endif
                    <th style='background-color: #3A58A8;color: white ; position:sticky;top:0;'>Overall Sales</th>
                    <th style='background-color: #3A58A8;color: white ; position:sticky;top:0;'>Good Sales</th>
                    <th style='background-color:#3A58A8;color: white;  position:sticky;top:0;'>Conversion Rate</th>
                </tr>
            </thead>
            <tbody class = "status-by-rate-report scroller">
            </tbody>
            <thead class="total-state-count">
            </thead>
        </table>
    </div>
</div>

@push('scripts')
    <script>
    $(document).ready(function(){
        
    })
        function loadStatusByStateData(data)
        {
            $.ajax({
                url: '{{route("dashboard.status.by.state")}}',
                method:'post',
                data:data,
                success:function(data)
                {
                    if(data.status == 'success') {
                        $('#leads-by-state-table-loading').css('visibility','hidden');
                        overallSale = data.data.overallSale;
                        goodSale = data.data.goodSale;
                        conversionRate = data.data.conversionRate;
                        overallSalesName = data.data.overallSalesName;
                        tableData = [];
                        i=0;
                        
                        $.each(overallSale,function(k,v){
                            goodsale = 0;
                            conversionrate = '0.00';
                            
                            state = k.split('-')[1];
                            name = k.split('-')[0];
                            id  = name.split('#')[1];
                            name  = name.split('#')[0];
                            if(k in goodSale)
                            {
                                goodsale = goodSale[k];
                            }
                            if(k in conversionRate)
                            {
                                conversionrate = conversionRate[k];
                            }
                            @if($sId == '')
                                tableData[i++] = [id,state,name,v,goodsale,conversionrate+"%"];
                            @else
                            tableData[i++] = [id,state,v,goodsale,conversionrate+"%"];
                            @endif
                        });
                        
                        $.fn.dataTable.ext.errMode = 'none';
                        ajaxData(tableData);
                        
                        footerData = "<tr><th style='background-color:#3A58A8;color: white; text-align:center;position:sticky;bottom:0;left:0;'>Total</th><th style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'></th><th style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>"+data.data.overallSalesTotal+"</th><th style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>"+data.data.goodSalesTotal+"</th><th style='background-color:#3A58A8;color: white; text-align:center;position:sticky;bottom:0;left:0;'>"+data.data.conversionRateTotal+"%</th></tr>";
                        $('.total-state-count').html(footerData);
                    }
                    else
                    {
                        $('#leads-by-state-table-loading').css('visibility','hidden');
                        // tableData[0] = "No Record Found.";
                        // ajaxData(tableData);
                        @if($sId == '')
                        $('#status-by-rate-table thead').html("<tr class='acjin'><th style='background-color:#3A58A8; color: white ; position:sticky;top:0;'>State</th><th style='background-color:#3A58A8; color: white ; position:sticky;top:0;'>Sales Center Location</th><th style='background-color: #3A58A8;color: white ; position:sticky;top:0;width:40px;'>Overall Sales</th><th style='background-color: #3A58A8;color: white ; position:sticky;top:0;width:23px;'>Good Sales</th><th style='background-color:#3A58A8;color: white;  position:sticky;top:0;'>Conversion Rate</th></tr>");
                        @else
                            $('#status-by-rate-table thead').html("<tr class='acjin'><th style='background-color:#3A58A8; color: white ; position:sticky;top:0;'>State</th><th style='background-color: #3A58A8;color: white ; position:sticky;top:0;'>Overall Sales</th><th style='background-color: #3A58A8;color: white ; position:sticky;top:0;width:30px;'>Good Sales</th><th style='background-color:#3A58A8;color: white;  position:sticky;top:0;'>Conversion Rate</th></tr>");
                        @endif
                        $('.status-by-rate-report').html("<tr><td colspan=5>No record found</td></tr>");
                        $('.total-state-count').html('');
                        // footerData = "<tr><th style='background-color:#3A58A8;color: white; text-align:center;position:sticky;bottom:0;left:0;'>Total</th><th style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'></th><th style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>"+0+"</th><th style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>"+0+"</th><th style='background-color:#3A58A8;color: white; text-align:center;position:sticky;bottom:0;left:0;'>0.00%</th></tr>";
                        // $('.total-state-count').html(footerData);
                    }

                }
            });
        }
        function ajaxData(tableData)
        {
            let table = $('#status-by-rate-table');
            var tabledata;
            $('#status-by-rate-table').DataTable({
                
                retrieve: true,
                paging: false,
                info: false,
                searching: false,  
                orderable:true,  
                columns: [
                    {
                    title: 'Id',
                },
                {
                    title: 'State',
                },
                @if($sId == '')
                {
                    title: 'Sales Center Location',
                },
                @endif
                {
                    title: 'Overall Sales',
                }, 
                {
                    title: 'Good Sales',
                }, 
                {
                    title: 'Conversion Rate',
                }
            ],
            columnDefs: [
                {
                    targets: [ 0 ],
                    visible: false
                }
            ]
            // rowGroup: {
            //     dataSrc: 0
            // },
            // rowsGroup: [
            //     'state:name'
            //     ],
                

            });
            table.dataTable().fnClearTable();
            table.dataTable().fnAddData(tableData);
        }
        $(document).ready(function(){
            $("#status-by-rate-table").delegate("tbody tr", "click", function(){
                if($(this)[0].innerText == 'No record found')
                {
                    return false;
                }
                state = $('#status-by-rate-table').dataTable().fnGetData($(this))[1];
                locationId = $('#status-by-rate-table').dataTable().fnGetData($(this))[0];
                $.fn.dataTable.ext.errMode = 'none';
                // if(state != null)
                // {
                    $('#telesales-status-leads-modal .modal-title').html(state +' State Report');
                    $('.charthiddenfield #location_id').val(locationId);
                    salesCenter = $('.hidden-salescenter').val();
                    $('.charthiddenfield #sales_center_id').val(salesCenter);
                    $('.charthiddenfield #state').val(state);
                    $('.charthiddenfield #sheet_name').val(state+" Report");
                    $('.charthiddenfield #sheet_title').val(state+" Report");
                    getTelesalesLeadsByStatus('','',salesCenter,'','','','',locationId,'','','','','','',state);
                // }
            });
        });
    </script>
@endpush
