<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='leads-by-sales-center-location-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Sales Center Location<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "leads-by-sales-center-location"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="leads-by-sales-center-location" style="width: 100%; height: 220px"></div>
</div>

@push('scripts')
    <script>
        function loadLeadsBySalesCenterLocationData(data)
        {
            $.ajax({
                url: '{{route("dashboard.leads-by-sclocations")}}',
                method:'post',
                data:data,
                success:function(res)
                {
                    if(res.status == 'success')
                    {
                        $('#leads-by-sales-center-location-loading').css('visibility','hidden');
                        resData = res.data;
                        loadLeadsByScLocationBarChart("leads-by-sales-center-location", resData);
                    }
                }
            });

        }

        function loadLeadsByScLocationBarChart(chartId, resData){
            var locationId = -1;
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
                    formatter: function(x) {
                        if(x.seriesType != 'line')
                        {

                        locationId = x.data.id;
                        $('.hidden-sales-location-id').attr('value', locationId);
                        $('.hidden-agent-id').attr('value', "");
                        $('.hidden-salescenter-id').attr('value', "");
                        var data = $("#deshbordNewForm").serializeArray();
                        $('.hidden-sales-location-id').attr('value', '');
                        var tooltip;
                        let chartId = "leads-by-sales-center-location"+locationId;
                        div = document.getElementById(chartId);
                        if(div == null)
                        {
                            var newDiv = document.createElement("div"); 
                            newDiv.setAttribute("id",chartId);
                            newDiv.style.width  ='300px';
                            newDiv.style.height = '217px';
                            parentDiv = document.getElementsByClassName('tooltip-chart-container')[0];
                            parentDiv.appendChild(newDiv);  
                            loadSalesCenterpieChartData(data,chartId,x.name);
                        }
                        chart =  echarts.init(document.getElementById(chartId));
                        let src = chart.getConnectedDataURL({
                            type:'png',
                            pixelRatio: 1,
                            backgroundColor: '#fff'
                        });
                        tooltip = "<img src='"+src+"'>";
                        return tooltip;
                        }
                        // if(x.seriesType == 'line')
                        // {
                        //     return x.name + ": " + parseFloat(x.value).toFixed(2)+"%";    
                        // }
                        // else
                        //     return x.name + ": " + x.value;
                    }
                },
                legend: {
                    show:true,
                    data: resData.name,
                    bottom: 0,
                    itemGap:itemGap,
                    itemWidth:itemWidth,
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
                xAxis: [
                    {
                        type: 'category',
                        data: resData.name,
                        name:'Sale Center Location',
                        nameLocation: 'middle',
                        nameGap: 50,
                        nameTextStyle:textStyle,
                        axisPointer: {
                            type: 'shadow'
                        },
                        axisLabel:{
                            formatter:function(d)
                            {
                                return d.replace(' ','\n');
                            },
                            interval:0,
                            rotate:50,
                            fontSize: '@php echo $countFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif'
                        },
                        axisTick: {
                            show:false
                        },
                        axisLine:{
                            show:false
                        },

                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: 'Leads',
                        nameGap:30,
                        nameLocation: 'middle',
                        nameTextStyle:textStyle,
                        splitLine:{
                            show:false
                        },
                        axisTick: {
                            show:false
                        },
                        axisLabel: {
                            formatter: '{value}',
                            fontSize: '@php echo $labelFontSize; @endphp',
                            fontFamily:'"DINRegular", sans-serif'
                        },
                        axisLine:{
                            show:false
                        },
                 
                    },
                    {
                        type: 'value',
                        name: 'Conversion Rate',
                        nameLocation: 'middle',
                        nameGap:40,
                        nameTextStyle:textStyle,
                        axisLabel: {
                            formatter: '{value}%',
                            fontSize: '@php echo $labelFontSize; @endphp',
                            color:'#B05150',
                            fontFamily:'"DINRegular", sans-serif'
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
                    }
                ],
                grid: {
                    top:'10%',
                    left: '9%',
                    right: '10%',
                    bottom: '10%',
                    containLabel: true
                },
                series:[
                    {
                        id: resData.id,
                        name: 'leads',
                        type: 'bar',
                        barMaxWidth:barMaxWidth,
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
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
                        data: resData.data

                    },
                    {
                        name: 'conversion rate',
                        type: 'line',
                        left: 30,
                        symbolSize: 3,
                        symbol:'circle',
                        yAxisIndex: 1,
                        label: {
                            show: true,
                            position: 'top',
                            padding:[0,0,10,0],
                            fontSize: '@php echo $countFontSize; @endphp',
                            formatter:function(d)
                            {
                                if(d.value == 0)
                                    return '';
                                else
                                    return d.value+"%";
                            },
                            textStyle:{
                                fontWeight:'bold',
                                color:color,
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }
                        },
                        // lineStyle: {
                        //     color:'#727cb5'
                        // },
                        itemStyle: {
                            color: '#B05150'
                        },
                        data: resData.rates
                    }
                ]
            };
            chart.setOption(option);
            chart.off('click');
            chart.on('click', function (params) {
                
                // if(!(data in params) && params.value == 0)
                // {
                // }
                // else
                // {
                    // $('.hidden-sales-location-id').attr('value', locationId);
                    // $('.hidden-agent-id').attr('value', "");
                    // $('.hidden-salescenter-id').attr('value', "");
                    // var data = $("#deshbordNewForm").serializeArray();
                    // $('.salescenter-name').html(params.name);
                    // loadSalesCenterpieChartData(data);
                    
                    $('.charthiddenfield #location_id').val(locationId);
                    let viewName = params.name;
                    $('.charthiddenfield #sheet_name').val(viewName+" Leads Report");
                    $('.charthiddenfield #sheet_title').val(viewName+" Leads Report");
                    $('#telesales-status-leads-modal .modal-title').html(viewName + " leads report");
                    getTelesalesLeadsByStatus(viewName,'','', '', '', '', '',locationId);
                // }
            });
        
               
                
        }
    </script>
@endpush
