<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='salescenter-location-leads-by-channel-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Channel<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "salescenter-location-leads-by-channel"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="salescenter-location-leads-by-channel" style="width: 100%; height: 220px"></div>
</div>


@push('scripts')
<script>
function loadSalesCenterLocationChannelBarChartData(data)
{
    $.ajax({
        url: '{{route("dashboard.leads-by-salescenter-location-channel")}}',
        method:'post',
        data:data,
        success:function(data)
        {   
            if(data.status == 'success')
            {
                $('#salescenter-location-leads-by-channel-loading').css('visibility','hidden');
                if (data.data.salesCenterList.length <= 0) {
                    document.getElementById("salescenter-location-leads-by-channel").innerHTML = "";
                    $("#salescenter-location-leads-by-channel").removeAttr("_echarts_instance_");
                    return false;
                 }
                else{
                    
                    salesCenterList = data.data;
                    loadSalesCenterChannelBarChart("salescenter-location-leads-by-channel",salesCenterList);
                }
            }
        }
    });
    
}

function loadSalesCenterChannelBarChart(chartId, chartData){
    locationId = "";
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
    var colors = @php echo json_encode($colors); @endphp;
    var setSeriesData = [];
        var setLegend = chartData.channel;
        if(chartData.locationNames.length > 3)
            {
                rotate = 50;
            }
            else
                rotate = 0;
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
                    fontWeight:'bold',
                    fontFamily:'"DINRegular", sans-serif',
                    fontSize: '@php echo $countFontSize; @endphp',
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
        $.each(chartData.salesCenterList, function(index, value) {
            setSeriesData.push({
                name: index,
                type: 'bar',
                stack: true,
                label: labelOption,
                barGap:'5%',
                barMaxWidth:barMaxWidth,
                data: value,
                
            });
        });
        option = {
            color: colors,
            tooltip: {

                trigger: 'item',
                axisPointer: {
                    type: 'shadow'
                },
                backgroundColor:backgroundColor,
                borderColor:borderColor,
                borderWidth:borderWidth,
                textStyle:tooltiptextStyle,
                formatter: function(x)
                {
                    locationName = x.name.split('#')[0];
                    locationId = x.name.split('#')[1];
                    return x.seriesName+"<br/>"+locationName +":"+ x.value;
                }
            },
            legend: {
                data: setLegend,
                bottom: 0,
                itemGap:itemGap,
                itemWidth:itemWidth,
                icon : '@php echo $legendIcon; @endphp',
                textStyle:legendStyle,
                formatter:function(l)
                {
                    if(l == 'D2D Sales')
                        return 'Door-to-Door';
                    else
                        return 'Telemarketing';
                },
            },
            grid: {
                top:5,
                left: 50, 
                bottom:80
            },
            calculable: true,
            xAxis: [{
                type: 'category',
                name:'Sales Center Location',
                nameLocation: 'middle',
                nameTextStyle:textStyle,
                nameGap: 50,
                left:0,
                fontSize: '@php echo $labelFontSize; @endphp',
                axisLabel: {
                    interval: 0,
                    rotate:rotate,
                    fontSize: '@php echo $countFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif',
                    formatter: function(d)
                    {
                        locationName = d.split('#')[0];
                        return locationName.replace(' ','\n');
                    }
                },
                axisLine:{
                    show:false
                },
                axisTick: {
                    show:false
                },
                data: chartData.locationNames
            }],
            yAxis: [{
                type: 'value',
                name:'Leads',
                nameLocation: 'middle',
                nameTextStyle:textStyle,
                nameGap: 30,
                axisLabel: {
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif',
                    formatter:function(d)
                    {
                        if(d%1 != 0)
                            return '';
                        else
                            return d;
                }
                },
                splitLine:{
                    show:false
                },
                axisLine:{
                    show:false
                },
                axisTick: {
                    show:false
                },
                

            }],
            series: setSeriesData
        }
        chart.setOption(option);
        chart.off('click');
        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName +' Report');
            $('.charthiddenfield #status').val(params.name);
            // $('.hidden-sales-location-id').attr('value', locationId);
            if(params.seriesName == "Tele Sales")
            {
                type = 'tele';
            }
            else
                type = 'd2d';
            $('.charthiddenfield #channelType').val(type);
            $('.charthiddenfield #location_id').val(locationId);
            $('.charthiddenfield #sheet_name').val(params.seriesName+" Report");
            $('.charthiddenfield #sheet_title').val(params.seriesName+" "+params.name.split("#")[0]+" Report");
            getTelesalesLeadsByStatus('', '','',type, '', '', '',locationId);
        });
    }
</script>
@endpush