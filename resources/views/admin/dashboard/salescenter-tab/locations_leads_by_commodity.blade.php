<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='leads-by-commodity-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads By Commodity<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "leads-by-commodity"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="leads-by-commodity" style="width: 100%; height: 220px"></div>
</div>


@push('scripts')
    <script>
        function loadLeadByCommodityBarChartData(data)
        {
            $.ajax({
                url: '{{route("dashboard.leads-by-commodity")}}',
                method:'post',
                data:data,
                success:function(res) {
                    if (res.status == "success") {
                        $('#leads-by-commodity-loading').css('visibility','hidden');
                        if(res.data.leadsCount.length <=0)
                        {
                            document.getElementById("leads-by-commodity").innerHTML = "";
                            $("#leads-by-commodity").removeAttr("_echarts_instance_");
                        return false;
                        }
                        loadLeadsByCommodityBarChart("leads-by-commodity", res.data.legendText, res.data.leadsCount, res.data.locationsNames);
                    }
                }
            });

        }

        function loadLeadsByCommodityBarChart(chartId, legendsList, data, locationNames){
            var setSeriesData = [];
            locationId = "";
            commodityId = "";
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
            if(locationNames.length > 3)
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
            $.each(data, function(index, value) {
                setSeriesData.push({
                    name: index,
                    type: 'bar',
                    stack: true,
                    label: labelOption,
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
                        commodityId = x.seriesName.split('#')[1];
                        x.seriesName = x.seriesName.split('#')[0];
                        locationId = x.name.split('#')[1];
                        return  x.seriesName + ": " + [x.value] + "<br/>";
                    }
                },
                legend: {
                    data: legendsList,
                    bottom: 0,
                    itemGap:itemGap,
                    itemWidth:itemWidth,
                    icon:'@php echo $legendIcon; @endphp',
                    textStyle:legendStyle,
                    type:'scroll',
                    pageIcons:{
                        horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                    }, 
                    pageIconSize:pageIconSize,
                    pageFormatter: pageFormatter,
                    pageButtonItemGap:pageButtonItemGap,
                    formatter:function(d)
                    {
                        return d.split('#')[0];
                    }
                },
                grid: {
                    top:5,
                    left: 50, 
                    bottom:80
            },
                calculable: true,
                xAxis: [{
                    type: 'category',
                    name:'Sale Center Location',
                    nameLocation: 'middle',
                    nameGap: 50,
                    nameTextStyle:textStyle,
                    left:10,
                    axisLabel: {
                        interval: 0,
                        rotate:rotate,
                        formatter: function(d)
                        {
                            d = d.split('#')[0];
                            return d.replace(' ','\n');
                        },
                        fontSize: '@php echo $countFontSize; @endphp',
                        fontFamily:'"DINRegular", sans-serif'
                    },
                    axisTick: {
                        show:false
                    },
                    axisLine:{
                        show:false
                    },
                    data: locationNames
                }],
                yAxis: [{
                    type: 'value',
                    name:'Leads',
                    nameLocation: 'middle',
                    nameTextStyle:textStyle,
                    nameGap: 30,
                    axisLabel: {
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
                    splitLine:{
                        show:false
                    },
                    axisTick: {
                        show:false
                    },
                    axisLine:{
                        show:false
                    },
                   
                }],
                series: setSeriesData
            }
            chart.setOption(option);
            chart.off('click');
            chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.name.split('#')[0] +' Report by '+ params.seriesName.split('#')[0]+' Commodity');
            $('.charthiddenfield #status').val(params.name);
            $('.charthiddenfield #sheet_name').val(params.name.split('#')[0] +' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.name.split('#')[0] +' Report by '+ params.seriesName.split('#')[0]+' Commodity');
            $('.charthiddenfield #commodity_type').val(commodityId);
            $('.charthiddenfield #location_id').val(locationId);
            $('.charthiddenfield #sales_center_id').val($('.hidden-salescenter').val());
            $('.charthiddenfield #locationCommodity').val('true');
            getTelesalesLeadsByStatus(params.name, '', $('.hidden-salescenter').val(), '',commodityId, '', '',locationId,'','','','true');
        });
        }
    </script>
@endpush
