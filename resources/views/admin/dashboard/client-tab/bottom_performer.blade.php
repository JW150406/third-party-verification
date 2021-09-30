<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='bottom-agents-by-conversion-rate-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Bottom 5 Performers<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "bottom-agents-by-conversion-rate"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
        <div id="bottom-agents-by-conversion-rate" style="width: 100%; height: {{$height}}px"></div>
</div>


@push('scripts')
    <script>
        function loadBottomPerformersData(data)
        {
            $.ajax({
                url: '{{route("dashboard.bottom-performers")}}',
                method:'post',
                data:data,
                success:function(data)
                {
                    if(data.status == 'success')
                    {
                        $('#bottom-agents-by-conversion-rate-loading').css('visibility','hidden');
                        agentsdata = data.data.agents;
                        names = data.data.names;
                        loadBottomPerformersBarChart("bottom-agents-by-conversion-rate",agentsdata,names);
                    }
                }
            });


        }

        function loadBottomPerformersBarChart(chartId,agentsdata,names){
            console.log(agentsdata);
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
            var agentName;
            chart.setOption({
                color: colorArr[5],
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
                        let chartId = "bottom-5-performers-"+agentId+"-"+locationId;
                        div = document.getElementById(chartId);
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
                    right: '4%',
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
                    formatter: '{value}%',
                    fontFamily:'"DINRegular", sans-serif',
                    fontSize: '@php echo $labelFontSize; @endphp',
                    color:'#000'
                },
                axisLine:{
                    show:false
                },
                axisTick: {
                    show:false
                }
                },
                yAxis: {
                    type: 'category',
                    data: names,

                    axisTick: {
                    show:false
                    },
                    axisLabel: {
                    formatter:function(d)
                    {
                        return d.replace(' ','\n');
                    },
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif'
                },
                axisLine:{
                    show:false
                }
                },
                series: [
                    {
                        // name: 'aaa',
                        type: 'bar',
                        // stack: '总量',
                        barMaxWidth:barMaxWidth,
                        label: {
                            show: true,
                            position: 'inside',
                            fontSize: '@php echo $countFontSize; @endphp',
                            formatter:function(x)
                            {
                                return x.value+"%";
                                // if(x.value != 0){
                                   
                                // }
                                // else{
                                //     return '';
                                // }
                            },
                            textStyle:{
                                fontWeight:'bold',
                                fontFamily:'"DINRegular", sans-serif',
                                fontSize: '@php echo $countFontSize; @endphp',
                            }

                        },
                        data: agentsdata,
                        fontFamily:'"DINRegular", sans-serif',
                        fontSize: '@php echo $labelFontSize; @endphp'
                    }
                ]
            });
            chart.off('click');
            chart.on('click', function (params) {

                // $('.hidden-agent-id').attr('value', agentId);
                // var data = $("#deshbordNewForm").serializeArray();
                // $('.salescenter-name').html(params.name.split('-')[0]);
                // loadSalesCenterpieChartData(data);
                
                $('.hidden-agent-id').attr('value', agentId);
                agentId = $('.hidden-agent-id').attr('value');
                locationId = $('.hidden-sales-location-id').attr('value');
                $('.charthiddenfield #agent_id').val(agentId);
                $('.charthiddenfield #location_id').val(locationId);
                let viewName = agentName;//$("#exampleModal .modal-dialog .salescenter-name").html();
                $('.charthiddenfield #sheet_name').val(viewName+" Leads Report");
                $('.charthiddenfield #sheet_title').val(viewName+" Leads Report");
                $('#telesales-status-leads-modal .modal-title').html(viewName + " leads report");
                
                getTelesalesLeadsByStatus(viewName,agentId,'', '', '', '', '',locationId);
        });

        }
    </script>
@endpush