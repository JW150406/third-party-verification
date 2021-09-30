<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='locations-wise-leads-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Sales Center Location<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "locations-wise-leads"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="locations-wise-leads" style="width: 100%; height: 220px"></div>
</div>

@push('scripts')
    <script>
        function loadLocationsWiseLeadsData(data)
        {
            $.ajax({
                url: '{{route("dashboard.locations-wise-leads")}}',
                method:'post',
                data:data,
                success:function(res)
                {
                    if(res.status == 'success')
                    {
                        $('#locations-wise-leads-loading').css('visibility','hidden');
                        resData = res.data;                        
                        loadLocationWiseLeadBarChart("locations-wise-leads", res.data.legendText,resData);
                    }
                }
            });
        }

        function loadLocationWiseLeadBarChart(chartId, legendList, resData){
            var locationId;
            var chart = echarts.init(document.getElementById(chartId));
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
            option = {
                color:colors,
                tooltip: {
                    trigger: 'item',
                    axisPointer: {
                        type: 'shadow'
                    },
                    backgroundColor:backgroundColor,
                    borderColor:borderColor,
                    borderWidth:borderWidth,
                    textStyle:tooltiptextStyle,
                    formatter: function(x) {
                        name = x.name.split('-')[0];
                        locationId = x.name.split('-')[1];
                        if(x.seriesType == 'line')
                        {
                            return x.seriesName+ "<br>" + name + " : " + x.value+"%";
                        }
                        else
                            return x.seriesName+ "<br>" + name + " : " + x.value;
                    }
                },
                // toolbox: {
                //     bottom:0,
                //     itemSize:@php echo $iconSize; @endphp,
                //     feature: {
                //         saveAsImage: {show: true,title:'Save',icon: downloadIcon}
                //     },
                    
                // },
                legend: {
                    bottom:0,
                    data:legendList,
                    itemGap:itemGap,
                    icon : '@php echo $legendIcon; @endphp',
                    textStyle:legendStyle,
                    type:'scroll',
                    itemWidth:itemWidth,
                    pageIcons:{
                        horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                    }, 
                    pageIconSize:pageIconSize,
                    pageFormatter: pageFormatter,
                    pageButtonItemGap:pageButtonItemGap
                },
                xAxis: [
                    {
                        type: 'category',
                        name:'Sale Center Location',
                        nameLocation: 'middle',
                        nameTextStyle:textStyle,
                        nameGap: 40,
                        axisLabel: {
                            interval: 0,
                            rotate: 0,
                            formatter: function(d)
                            {
                                d = d.split('-')[0];
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

                        data: resData.locationsNames
                    
                    }
                ],
                grid: {
                    top:20,
                    left: 40, 
                    bottom:80,
                    right:50
                },
                yAxis: [
                    {
                        type: 'value',
                        name: 'Leads',
                        nameLocation: 'middle',
                        nameGap:25,
                        nameTextStyle:textStyle,
                        axisLabel: {
                            formatter: '{value}',
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
                        axisTick: {
                            show:false
                        },
                        axisLine:{
                            show:false
                        },
                        
                    },
                    {
                        type: 'value',
                        name: 'Conversion Rate',
                        nameLocation: 'middle',
                        nameTextStyle:textStyle,
                        nameGap:35,
                        axisLabel: {
                            formatter: '{value}%',
                            color:'#000',
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
                    }
                ],
                series: [
                    {
                        name: 'Good Sale',
                        type: 'bar',
                        barMaxWidth:15,
                        barGap:'0%',
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif',
                            formatter:function(d)
                            {
                                if(d.value == 0)
                                    return '';
                                else
                                    return d.value;
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }
                        },
                        data: resData.good_sales
                    },
                    {
                        name: 'Pending Leads',
                        type: 'bar',
                        barMaxWidth:15,
                        barGap:'0%',
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif',
                            formatter:function(d)
                            {
                                if(d.value == 0)
                                    return '';
                                else
                                    return d.value;
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }
                        },
                        data: resData.pending_leads
                    },
                    {
                        name: 'Bad Sale',
                        type: 'bar',
                        barMaxWidth:15,
                        barGap:'0%',
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif',
                            formatter:function(d)
                            {
                                if(d.value == 0)
                                    return '';
                                else
                                    return d.value;
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }
                        },
                        data: resData.bad_sales
                    },
                    {
                        name: 'Cancelled Leads',
                        type: 'bar',
                        barMaxWidth:15,
                        barGap:'0%',
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif',
                            formatter:function(d)
                            {
                                if(d.value == 0)
                                    return '';
                                else
                                    return d.value;
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }
                        },
                        data: resData.cancelled_leads
                    },
                    {
                        name: 'conversion rate',
                        type: 'line',
                        symbolSize: 3,
                        symbol:'circle',
                        yAxisIndex: 1,
                        label: {
                            show: true,
                            position: 'top',
                            padding:[10,0,0,0],
                            fontSize: '@php echo $countFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif',
                            formatter:function(d)
                            {
                                if(d.value == 0)
                                    return '';
                                else
                                    return d.value+"%";
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                color:'#000',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }
                        },
                        itemStyle: {
                            color: '#B05150'
                        },
                        data: resData.rates
                    }
                ]
            };
            chart.setOption(option);
            chart.off('click');
            chart.on('click', function(params) {
                if(params.value == 0)
                {
                }
                else
                {
                    $('#telesales-status-leads-modal .modal-title').html(params.seriesName +' Report');
                    $('.charthiddenfield #status').val(params.seriesName);
                    salesCenterId = $('.hidden-salescenter').attr('value');
                    $('.charthiddenfield #location_id').val(locationId);
                    $('.charthiddenfield #sales_center_id').val(salesCenterId);
                    $('.charthiddenfield #sheet_name').val(params.seriesName+" Report");
                    $('.charthiddenfield #sheet_title').val(params.seriesName+" Report");
                    getTelesalesLeadsByStatus(params.seriesName, '',salesCenterId, '', '', '', '',locationId);
                }
        });
        }
    </script>
@endpush
