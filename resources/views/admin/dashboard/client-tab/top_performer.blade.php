<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='top-agents-by-conversion-rate-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Top 5 Performers<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "top-agents-by-conversion-rate"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
<div id="top-agents-by-conversion-rate" style="width: 100%;height : {{$height}}px" ></div>
</div>


@push('scripts')
    <script>
        function loadTopPerformersData(data)
        {
            $.ajax({
                url: '{{route("dashboard.top-performers")}}',
                method:'post',
                data:data,
                success:function(res)
                {
                    if(res.status == 'success')
                    {
                        $('#top-agents-by-conversion-rate-loading').css('visibility','hidden');
                        let agentsdata = res.data.agents;
                        let names = res.data.names;
                        loadTopPerformersBarChart("top-agents-by-conversion-rate", agentsdata, names);
                    }
                }
            });
        }

        function loadTopPerformersBarChart(chartId,agentsdata,names) {
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
            var colorArr = @php echo json_encode($colors); @endphp;
            var agentId;
            var agentName = "";
            chart.setOption({
                color: colorArr[0],
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
                    textStyle:{
                        color:'#3A58A8',
                        fontSize:12,
                        fontFamily:'"DINRegular", sans-serif'
                    },
                    formatter: function(x) {
                        agentId = x.data.id;
                        agentName = x.name;
                        $('.hidden-agent-id').attr('value', agentId);
                        locationId = $('.hidden-sales-location-id').attr('value');
                        salesCenterId = $('.hidden-salescenter').attr('value');
                        var data = $("#deshbordNewForm").serializeArray();
                        var tooltip;
                        let chartId = "top-5-performers-"+agentId+"-"+locationId;
                        let div = document.getElementById(chartId);
                        if(div == null)
                        {
                            var newDiv = document.createElement("div"); 
                            newDiv.setAttribute("id",chartId);
                            newDiv.style.width  ='300px';
                            newDiv.style.height = '217px';
                            parentDiv = document.getElementsByClassName('tooltip-chart-container')[0];
                            parentDiv.appendChild(newDiv);  
                            loadSalesCenterpieChartData(data,chartId,agentName);
                        }
                        chart =  echarts.init(document.getElementById(chartId));
                        let src = chart.getConnectedDataURL({
                            type:'png',
                            pixelRatio: 1,
                            backgroundColor: '#fff'
                        });
                        tooltip = "<img src='"+src+"'>";
                        return tooltip;
                        // return x.name + ": " + x.value +"%";
                    }
                },
                grid: {
                    top:'10%',
                    left: '3%',
                    right: '5%',
                    bottom: '10%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    name: 'Conversion Rate',
                    nameLocation: 'middle',
                    nameGap:22,
                    nameTextStyle:textStyle,
                    splitLine:{
                    show:false
                },
                axisLabel:{
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif',
                    formatter: '{value}%',
                    color:'#000'
                },
                axisTick: {
                    show:false
                },
                axisLine:{
                    show:false
                }
                },
                yAxis: {
                    type: 'category',
                    // name: 'Conversion Rate',
                    // nameLocation: 'middle',
                    boundaryGap: ['50%', '50%'],
                    data: names,
                    axisLabel: {
                    formatter:function(d)
                    {
                        return d.replace(' ','\n');
                    },
                    fontSize: 8,
                    fontFamily:'"DINRegular", sans-serif'
                    
                },
                axisTick: {
                    show:false
                    },
                lineStyle: {
                        opacity:0
                    },
                    axisLine:{
                        show:false
                    }
            },
                series: [
                    {
                        name:'a',
                        type: 'bar',
                        stack: '总量',
                        barMaxWidth:barMaxWidth,
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
                            formatter:function(x)
                            {
                                if(x.value != 0){
                                    return x.value+"%";
                                }
                                else{
                                    return '';
                                }
                                
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }

                        },
                        data: agentsdata

                    }
                ]
            });
            chart.off('click');
            chart.on('click', function (params) {
                
                $('.hidden-agent-id').attr('value', agentId);
                agentId = $('.hidden-agent-id').attr('value');
                let locationId = $('.hidden-sales-location-id').attr('value');
                $('.charthiddenfield #agent_id').val(agentId);
                $('.charthiddenfield #location_id').val(locationId);
                let viewName = agentName;// $("#exampleModal .modal-dialog .salescenter-name").html();
                $('.charthiddenfield #sheet_name').val(viewName+" Leads Report");
                $('.charthiddenfield #sheet_title').val(viewName+" Leads Report");
                $('#telesales-status-leads-modal .modal-title').html(viewName + " leads report");
                $('.charthiddenfield #brand').val($('.hidden-brand').attr('value'));
                $('#telesales-status-leads').DataTable().destroy();
                getTelesalesLeadsByStatus(viewName,agentId,'', '', '', '', '',locationId);
        });
        }
    </script>
@endpush
