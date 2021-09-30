<div class="dashboard-box" id = "client-leads">
<span class="dashboard-spiner-icon" id ='client-leads-pie-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Status<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "client-leads-pie"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    
    <div id="client-leads-pie" style="width: 100%; height: 217px;"></div>
</div>

@push('scripts')
<style>
/* #tooltip-show-chart thead tr td{
    padding-top:4px;
    padding-bottom:4px;
}
#tooltip-show-chart tbody tr td{
    padding-top:4px;
    padding-bottom:4px;
    text-align:left;
}
#tooltip-show-chart tbody tr td:last-child{
    text-align:center;
} */
    
</style>
<script>
function loadClientPieChartData(data)
{

    $.ajax({
        url: '{{route("dashboard.load.client.pie.data")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                $('#client-leads-pie-loading').css('visibility','hidden');
                clientData = jQuery.parseJSON(data.data.clientData);
                salesCenterData = data.data.salesCenterDetails;
                loadClientPieChart("client-leads-pie",data.data.status,clientData,salesCenterData);
            }
        }
    });

}

function loadClientPieChart(chartId, statusList, statusData,salesCenterData) {
        var chart = echarts.init(document.getElementById(chartId)); 
        <?php      
        if($identifier == 'mobile')
        {
            $colors = implode(',',colorArray());    
        }
        else
        {
            if(isset($colors))
            {
                $colors = explode(',',$colors);
                if(count($colors) <= 1)
                {
                    $colors = colorArray();
                } 
            }
            else
            {
                $colors = implode(',',colorArray());
            }
        }
        ?>

        $.each(statusData, function( key, value ) {
            switch(value["name"]) {
                case "Good Sale":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[0]}}',
                    }
                    break;
                case "Pending Leads":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[1]}}',
                    }
                    break;
                case "Bad Sale":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[2]}}',
                    }
                    break;
                case "Cancelled Leads":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[3]}}',
                    }
                    break;
            }
        });
        var radius = [40, 55];
        chart.setOption({
            tooltip: {
                trigger: 'item',
                backgroundColor:backgroundColor,
                borderColor:borderColor,
                borderWidth:borderWidth,
                textStyle:tooltiptextStyle,
                formatter: function(x) {
                    let toolTip ='<div class="table-responsive"><table class="tooltip-show-chart"><thead><tr><td>Sales Center</td><td>Leads</td></tr></thead><tbody>';
                    $.each(salesCenterData,function(key,val){
                        $.each(val,function(k,v){   
                            if(v['name'] == x.name){
                                toolTip +="<tr><td>"+ key + " </td><td> " + v['value'] + "</td></tr>";
                            }
                        });
                    });
                    toolTip +="</tbody></table></div>";
                    return toolTip;
                }
            },
            legend: {
                orient: 'horizontal',
                bottom: 0,
                itemWidth:itemWidth,
                itemGap:itemGap,
                data: statusList,
                label: "Sales Center",
                type: 'scroll',
                icon : '@php echo $legendIcon; @endphp',
                fontSize: '@php echo $labelFontSize; @endphp',
                textStyle: legendStyle,
                pageIcons:{
                    horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                }, 
                pageIconSize:pageIconSize,
                pageFormatter: pageFormatter,
                pageButtonItemGap:pageButtonItemGap,
            },
            calculable: true,
            series: [{
                name: 'Status',
                type: 'pie',
                radius: '55%',
                selectedMode: 'single',
                selectedOffset: 5,
                hoverOffset:hoverOffset,
                avoidLabelOverlap: true,
                top:'0%',
                bottom:'0%',
                center:['50%','45%'],
                labelLine: {
                    lineStyle: {
                        color: 'rgba(0, 0, 0, 1)'
                    }
                },
                color: @php echo json_encode($colors) @endphp,
                label: {
                    show: true,
                    position: "outside",
                    rotate: true,
                    color:color,
                    fontSize:'@php echo $countFontSize; @endphp',
                    formatter: function(x) {
                    return  x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                },
                    rotate: 0,
                },
                data: statusData
            }]
        });
        chart.off('click');
        chart.on('click', function(params) {
           
            $('#telesales-status-leads-modal .modal-title').html(params.name +' Verification Status');
            $('.charthiddenfield #status').val(params.name);
            $('.charthiddenfield #sheet_name').val(params.name+" Report");
            $('.charthiddenfield #sheet_title').val(params.name+" Report");
            getTelesalesLeadsByStatus(params.name, '','', '', '', '', '','');
        });        

       
    }

</script>
@endpush
