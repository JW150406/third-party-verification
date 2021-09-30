<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='salescenter-leads-by-channel-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Channel<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "salescenter-leads-by-channel"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="salescenter-leads-by-channel" style="width: 100%; height: 220px"></div><!--200-->
</div>


@push('scripts')
<script>
function loadSalesCenterChannelBarChartData(data)
{
    $.ajax({
        url: '{{route("dashboard.salescenter.channel.data")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                $('#salescenter-leads-by-channel-loading').css('visibility','hidden');
                if (data.data.salesCenterChannelData.length <= 0) {
                    document.getElementById("salescenter-leads-by-channel").innerHTML = "";
                    $("#salescenter-leads-by-channel").removeAttr("_echarts_instance_");
                    return false;
                 }
                else{

                    salesCenterChannelData = data.data.salesCenterChannelData;
                    loadSalesCenterChannelBarChart("salescenter-leads-by-channel",data.data.channel,salesCenterChannelData,data.data.status,data.data.salesCenterToolTipData,data.data.tooltipFalse,data.data);
                }
            }
        }
    });

}

function loadSalesCenterChannelBarChart(chartId, statusList,getChartData,xaxis,salesCenterToolTipData,tooltipFalse,rateData){
   backgroundColor = '#fff';
        borderColor = "#000";
        borderWidth = 1;
        tooltiptextStyle = {
                    color:'#3A58A8',
                    fontSize:12,
                }
    <?php
     
        if($identifier == 'mobile')
        {
            $colors = colorArray();    
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
                $colors = colorArray();
            }
        }
    ?>
            
    var setSeriesData = [];
        var setLegend = statusList;
        var chart = echarts.init(document.getElementById(chartId));
        var labelOption = {
            normal: {
                show: true,
                position: 'inside',
                distance: 0,
                rotate: 0,
                textStyle: {
                    align: 'center',
                    verticalAlign: 'middle',
                    fontWeight:800,
                    fontSize: '@php echo $countFontSize; @endphp'
                },
                formatter:function(d)
                {
                    if(d.value != 0){
                        return d.value;
                    }
                    else{
                        return '';
                    }
                }
            }
        };
        $.each(getChartData, function(index, value) {
            setSeriesData.push({
                name: index,
                type: 'bar',
                stack: true,
                barMaxWidth:barMaxWidth,
                label: labelOption,
                data: value,
                fontWeight:800,
                
                
            });
        });
        option = {
            color: @php echo json_encode($colors) @endphp,
            tooltip: {
                trigger: 'item',
                @if($identifier == 'mobile')
                    position: [10, -100],   
                @endif
                axisPointer: {
                    type: 'shadow'
                },
                backgroundColor:backgroundColor,
                borderColor:borderColor,
                borderWidth:borderWidth,
                textStyle:tooltiptextStyle,
                formatter: function(x)
                {
                    let toolTip;
                    if(tooltipFalse == false)
                    {
                        toolTip = '<div class="table-responsive"><table class="tooltip-show-chart"><thead><tr><td>Sales Center</td><td>Leads</td></tr></thead><tbody>';
                    }
                    else
                        toolTip = '<div class="table-responsive"><table class="tooltip-show-chart"><thead><tr><td style="padding-right:5px;">Sales Center Location</td><td>Leads</td><td>Conversion Rate</td></tr></thead><tbody>';
                        $.each(salesCenterToolTipData,function(key,val){
                            if(key == x.seriesName){
                                $.each(val,function(k,v){
                                    if(v[x.name]){
                                        toolTip += "<tr>";
                                        toolTip += "<td>"+k + "</td><td>" + v[x.name] + "</td>";
                                        if(tooltipFalse == true)
                                        {
                                            toolTip += "<td>"+rateData[x.seriesName]+" %</td></tr>";
                                        }
                                        else
                                            toolTip += "</tr>";
                                    }
                                });
                            }   
                        });
                        toolTip +="</tbody></table></div>";
                    
                    return toolTip;
                }
            },
            legend: {
                
                data: setLegend,
                bottom: 0,
                itemGap:itemGap,
                itemWidth:itemWidth,
                formatter:function(l)
                {
                    if(l == 'D2D Sales')
                        return 'Door-to-Door';
                    else
                        return 'Telemarketing';
                },
                icon:'circle',
                itemSize:10,
                textStyle: legendStyle,
                pageIcons:{
                    horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                }, 
                pageIconSize:pageIconSize,
                pageFormatter: pageFormatter,
                pageButtonItemGap:pageButtonItemGap
            },
            calculable: true,
            grid: {
                    top:'10%',
                    left: '10%',
                    right: '4%',
                    bottom: '20%',
                    containLabel: true
                },
            xAxis: [{
                type: 'category',
                name:'Status',
                nameLocation: 'middle',
                nameGap: 30,
                left:10,
                nameTextStyle:textStyle,
                axisTick: {
                    show:false
                },
                axisLabel: {
                    interval: 0,
                    rotate: 0,
                    formatter: function(d)
                    {
                        return d.replace(' ','\n');
                    },
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif'
                },
                axisLine:{
                    show:false
                },
                data: xaxis
            }],
            yAxis: [{
                type: 'value',
                name:'Leads',
                nameLocation: 'middle',
                nameTextStyle: {
                    fontWeight:'bold',
                    color:'#000',
                    fontFamily:'"DINRegular", sans-serif',
                    fontSize: '@php echo $labelFontSize; @endphp',
                },
                nameGap: 27,
                splitLine:{
                    show:false
                },
                axisTick: {
                    show:false
                },
                axisLabel:{
                    formatter:function(d)
                    {
                        if(d%1 != 0)
                            return '';
                        else
                            return d;
                    },
                    
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif'
                },
                axisLine:{
                    show:false
                }

            }],
            series: setSeriesData
        }
        chart.setOption(option);
        chart.off('click');
        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.name +' Report');
            $('.charthiddenfield #status').val(params.name);
            if(params.seriesName == "Tele Sales")
            {
                type = 'tele';
            }
            else
                type = 'd2d';
            $('.charthiddenfield #channelType').val(type);
            $('.charthiddenfield #sheet_name').val(params.name+" Report");
            $('.charthiddenfield #sheet_title').val(params.seriesName+" "+params.name+" Report");
            locationId = $('.hidden-sales-location-id').val();
            $('.charthiddenfield #location_id').val(locationId);
            salesCenter = $('.hidden-salescenter').val();
            $('.charthiddenfield #sales_center_id').val(salesCenter);
            getTelesalesLeadsByStatus(params.name, '',salesCenter,type, '', '', '',locationId);
        });
    }
</script>
@endpush
