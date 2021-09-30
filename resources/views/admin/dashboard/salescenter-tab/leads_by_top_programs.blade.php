<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='programs-leads-donut-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Top 5 Programs<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "programs-leads-donut"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    
    <div id="programs-leads-donut" style="width: 100%; height:220px;"></div>
</div>

@push('scripts')
<script>
function loadProgramsLeadsDonutChartData(data)
{

    $.ajax({
        url: '{{route("dashboard.load.top.programs.donut")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                $('#programs-leads-donut-loading').css('visibility','hidden');
                loadProgramsLeadsDonutChart("programs-leads-donut",data.data.name,data.data.programs,data.data.salesCenters);
            }
        }
    });

}

function loadProgramsLeadsDonutChart(chartId, statusList, statusData,salesCenterData) {
        var chart = echarts.init(document.getElementById(chartId));
        var salesCenterId;
        var programId;
        var salesCenterNames = [];
        var radius = [35, 40];
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
        chart.setOption({
            tooltip: {
                trigger: 'item',
                @if($identifier == 'mobile')
                    position: [10, -100],   
                @endif
                backgroundColor:'#fff',
                borderColor:'#000',
                borderWidth:1,
                textStyle:{
                    color:'#3A58A8',
                    fontSize:12
                },
                formatter: function(x) {
                    programId = x.data.program_id;
                    toolTip ='<div class="table-responsive"><table class="tooltip-show-chart"><thead><tr><td>Sales Center</td><td>Leads</td><td>Conversion Rate</td></tr></thead><tbody>';
                    $.each(salesCenterData,function(key,val){
                        if(key == programId){
                        $.each(val,function(k,v){
                            toolTip += "<tr>";
                            toolTip +="<td>"+ v['name'] + " </td><td> " + v['count'] + "</td><td>"+v['conversionRate']+"%</td></tr>";
                            });
                        }
                    });
                    toolTip +="</tbody></table></div>";
                    return toolTip;
                }
            },
            legend: {
                // formatter:function(x)
                // {
                //     salesCenter = x.split('-');
                //     return salesCenter[0];
                // },
                itemGap:itemGap,
                itemWidth: itemWidth,
                orient: 'horizontal',
                bottom: 0,
                distance:2,
                data: statusList,
                label: "Sales Center",
                type: 'scroll',
                icon : '@php echo $legendIcon; @endphp',
                fontSize: '@php echo $labelFontSize; @endphp',
                fontFamily:'"DINRegular", sans-serif',
                textStyle: legendStyle,
                type:'scroll',
                pageIcons:{
                    horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                }, 
                pageIconSize:pageIconSize,
                pageFormatter: pageFormatter,
                pageButtonItemGap:pageButtonItemGap
            },
            grid: {
                    top:'10%',
                    left: '3%',
                    right: '4%',
                    bottom: '10%'

                },
            calculable: true,
            series: [{
                name: 'Sales Center',
                type: 'pie',
                radius: ['40%', '60%'],
                avoidLabelOverlap: true,
                fontFamily:'"DINRegular", sans-serif',
                // selectedMode: 'single',
                // selectedOffset: 5,
                hoverOffset:hoverOffset,
                rotate:true,
                rotate: 45,
                top:0,
                left: 20,
                right: 20,
                bottom: 20,
                color: colors,
                labelLine: {
                    lineStyle: {
                        color: 'rgba(0, 0, 0, 1)'
                    }
                },
                label: {
                    show: true,
                    position: "outside",
                    rotate: true,
                    color:color,
                    lineStyle:{
                        color:'#000'
                    },
                
                    fontSize:'@php echo $countFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif',
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
            $('#telesales-status-leads-modal .modal-title').html(params.name +' Programs Report');
            $('.charthiddenfield #status').val(params.name);
            $('.charthiddenfield #program_id').val(programId);
            $('.charthiddenfield #sheet_name').val('Top 5 Programs Report');
            $('.charthiddenfield #sheet_title').val(params.name+" Programs Report");
            $('.charthiddenfield #brand').val($('.hidden-brand').attr('value'));
            getTelesalesLeadsByStatus('', '',salesCenterId, '', '', '', '','','','','','',programId);
        });
    }
</script>
@endpush
