<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='salescenter-leads-by-commodity-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Commodity<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "salescenter-leads-by-commodity"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="salescenter-leads-by-commodity" style="width: 100%; height: 220px;"></div>
</div>


@push('scripts')
<script>
function loadSalesCenterCommodityBarChartData(data)
{
    $.ajax({
        url: '{{route("dashboard.salescenter.commodity.data")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                $('#salescenter-leads-by-commodity-loading').css('visibility','hidden');
                if (data.data.salesCenterCommodityData.length <= 0) {
                    document.getElementById("salescenter-leads-by-commodity").innerHTML = "";
                    $("#salescenter-leads-by-commodity").removeAttr("_echarts_instance_");
                    return false;
                 }
                else{

                    salesCenterCommodityData = data.data.salesCenterCommodityData;
                    tooltipData = data.data.toolTipSalesCenterData;

                    loadSalesCenterCommodityBarChart("salescenter-leads-by-commodity",data.data.legendText,salesCenterCommodityData,data.data.channelList,tooltipData,data.data.tooltipFalse);
                }
            }
        }
    });

}

function loadSalesCenterCommodityBarChart(chartId, statusList,getChartData,xaxis,toolTipSalesCenterData,tooltipFalse){
        // backgroundColor = '#fff';
        // borderWidth = 1;
        // borderColor = "#000";
        // tooltiptextStyle = {
        //             color:'#3A58A8',
        //             fontSize:12,
        //         }

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
    commodityId = "";
        var setLegend = statusList;
        var i =0;
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
                label: labelOption,
                data: value,
                barMaxWidth:barMaxWidth,
                textStyle: textStyle,
                
            });
        });
        option = {
            color:@php echo json_encode($colors) @endphp,
            tooltip: {
                @if($identifier == 'mobile')
                    position: [10, -100],   
                @endif
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
                    
                    commodityId = x.seriesName.split('-')[1];
                    seriesName =  x.seriesName.split('-')[0];
                    let toolTip;
                    if(tooltipFalse === false)
                    {
                        toolTip = '<div class="table-responsive"><table class="tooltip-show-chart"><thead><tr><td>Sales Center</td><td>Leads</td></tr></thead><tbody>';
                    }
                    else
                    {
                        toolTip = '<div class="table-responsive"><table class="tooltip-show-chart"><thead><tr><td style="padding-right:5px;">Sales Center Location</td><td>Leads</td><td>Conversion Rate</td></tr></thead><tbody>';
                    }
                        $.each(toolTipSalesCenterData,function(key,val){
                            if(key == x.name){
                                
                            $.each(val[x.seriesName],function(k,v){
                                if(v){
                                    toolTip += "<tr>";
                                    toolTip += "<td>"+k + "</td><td>" + v + "</td>";
                                    if(tooltipFalse === true)
                                    {
                                        toolTip += "<td>" + toolTipSalesCenterData['rate'][x.seriesName][k] + "%</td></tr>";
                                    }else{
                                        toolTip += '</tr>';
                                    }
                                }
                            });
                            }
                        });
                        toolTip +="</tbody></table></div>";
                        return toolTip;
                    // }
                    // else
                    // {
                    //     return x.name+ "<br/>" +seriesName+": "+x.value;
                    // }
                }
            },
            legend: {
                data: setLegend,
                bottom: 0,
                itemGap:itemGap,
                itemWidth:itemWidth,
                type:'scroll',
                icon : '@php echo $legendIcon; @endphp',
                textStyle: legendStyle,
                pageIcons:{
                    horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                }, 
                pageIconSize:pageIconSize,
                pageFormatter: pageFormatter,
                pageButtonItemGap:pageButtonItemGap,
                formatter:function(d)
                {
                    return d.split('-')[0];
                }
            },
            grid: {
            left: 80,
            bottom: 80,
            top:20
        },
            // toolbox: {
            //     show: true,
            //     bottom:0,
            //     itemSize:@php echo $iconSize; @endphp,
            //     feature: {
            //         mark: {
            //             show: false
            //         },
            //         saveAsImage: {
            //             show: true,
            //             title: "Save",
            //             icon: downloadIcon
            //         },
            //     }
            // },
            calculable: true,
            yAxis: [{
                type: 'category',
                name:'Status',
                nameLocation: 'middle',
                nameGap: 60,
                nameTextStyle: textStyle,
                axisLabel: {
                    interval: 0,
                    rotate: 0,
                    left:50,
                    margin: 10,
                    formatter: function(d)
                    {
                        return d.replace(' ','\n');
                    },
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif'
                },
                axisTick: {
                    show:false
                },
                axisLine:{
                    show:false
                },
                data: xaxis,
                
            }],
            xAxis: [{
                type: 'value',
                name:'Leads',
                nameLocation: 'middle',
                nameTextStyle: textStyle,
                nameGap: 36,
                splitLine:{
                    show:false
                },
                axisTick: {
                    show:false
                },
                axisLine:{
                    show:false,
                    fontSize: '@php echo $labelFontSize; @endphp'
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

            }],
            series: setSeriesData
        }
        chart.setOption(option);
        chart.off('click');
        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.name +' Report by '+ params.seriesName.split('-')[0]+' Commodity');
            $('.charthiddenfield #status').val(params.name);
            $('.charthiddenfield #sheet_name').val(params.name +' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.name +' Report by '+ params.seriesName.split('-')[0]+' Commodity');
            $('.charthiddenfield #commodity_type').val(commodityId);
            locationId = $('.hidden-sales-location-id').val();
            $('.charthiddenfield #location_id').val(locationId);
            salesCenter = $('.hidden-salescenter').val();
            $('.charthiddenfield #sales_center_id').val(salesCenter);
            $('.charthiddenfield #brand').val($('.hidden-brand').attr('value'));
            getTelesalesLeadsByStatus(params.name, '', salesCenter, '', commodityId, '', '',locationId);
        });
    }
</script>
@endpush
