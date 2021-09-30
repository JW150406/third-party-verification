<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='leads-count-with-conversion-rate-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Conversion Rate v/s Leads<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "leads-count-with-conversion-rate"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>

    <div class="row">
        <div class="col-md-12 col-xs-12 col-sm-12">
            <div class="col-md-2 col-xs-2 col-sm-2"></div>
            <div class="col-md-3 col-xs-3 col-sm-3">
            <div id="line-day" attr="line-day" class="line-chart-filters linechart-div"></div><a class="line-chart-filters" attr="line-day" style="cursor:pointer;" style="color:#000;">Daily</a>
                </div>
                <div class="col-md-3 col-xs-3 col-sm-3">
                    <div id="line-month" attr="line-month" class="line-chart-filters linechart-div"></div><a class="line-chart-filters" attr="line-month" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" data-container="body" data-original-title="Month-Over-Month" style="color:#000;">MoM</a>
                </div>
                <div class="col-md-3 col-xs-3 col-sm-3">
                <div id="line-year" attr="line-year" class="line-chart-filters linechart-div"></div><a class="line-chart-filters" attr="line-year" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" data-container="body" data-original-title="Year-Over-Year" style="color:#000;">YoY</a>
                </div>
                <div class="col-md-1 col-xs-1 col-sm-1"></div>
            </div>
            <div class="col-md-12 col-xs-12 col-sm-12">
            <div id="leads-count-with-conversion-rate" style="width: 100%; height: {{$height}}px"></div>
        </div>
    </div>
</div>

@push('scripts')
<style>
.linechart-div{
    height:13px;
    width:13px; 
    border:1px solid black;
    float: left;
    margin-right: 7px;
    border-radius:2px;
    cursor:pointer;
}
</style>
<script>
$(document).ready(function(){
    $('.line-chart-filters').click(function(){
        attr = $(this).attr('attr');
        $('#leads-count-with-conversion-rate-loading').css('visibility','visible');
        $('.line-chart-filters').css('background-color','#fff');
        $('#'+attr).css('background-color','#000');
        // $('.hidden-line-filter-value').attr('value',$(this).attr('id').split('-')[1]);
        // $('.line-chart-filters').css('background-color','#fff');
        // $(this).css('background-color','#000');
        $('.hidden-line-filter-value').attr('value',attr.split('-')[1]);
        var data = $("#deshbordNewForm").serializeArray();
        loadLeadsCountLineChartData(data)
    });
});
function loadLeadsCountLineChartData(data)
{
    $.ajax({
        url: '{{route("dashboard.leads.count.rate.data")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                $('#leads-count-with-conversion-rate-loading').css('visibility','hidden');
                if (data.data.leads.length <= 0) {
                    document.getElementById("leads-count-with-conversion-rate").innerHTML = "";
                    $("#leads-count-with-conversion-rate").removeAttr("_echarts_instance_");
                    return false;
                 }
                else{
                    loadLeadsCountLineChart("leads-count-with-conversion-rate",data.data.leads,data.data.rate,data.data.xaxisDate);
                }
            }
        }
    });

}

function loadLeadsCountLineChart(chartId,leads,rate,xaxisDate){
    var chart = echarts.init(document.getElementById(chartId));
    <?php
        if($identifier == 'mobile')
        {
            $colors = colorArray();    
        }
        else{
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
    var colors = @php echo json_encode($colors) @endphp;
    @if($sId != "")
        nameTextStyle2 = {
        fontWeight:'bold',
        fontFamily:'"DINRegular", sans-serif',
        fontSize: '@php echo $labelFontSize; @endphp',
        color:'#000',
        align:'left',
        verficalAlign:'top',
        padding:[0,-12]
    };
    nameTextStyle1 = {
                    fontWeight:'bold',
                    fontFamily:'"DINRegular", sans-serif',
                    fontSize: '@php echo $labelFontSize; @endphp',
                    color:'#000',
                    align:'left',
                    verficalAlign:'top',
                    padding:[0,13]
                };
    @else
    nameTextStyle2 = textStyle;
    nameTextStyle1 = textStyle;
    @endif
    option = {
        color: colors,

        tooltip: {
            trigger: 'item',
            axisPointer: {
                type: 'cross'
            },
            backgroundColor:backgroundColor,
            borderColor:borderColor,
            borderWidth:borderWidth,
            textStyle:tooltiptextStyle,
            formatter:function(x)
            {
                // return x.seriesName+"<br/>"+x.value;
            }
        },
        legend: {
                data: ['Leads','Conversion Rate'], 
                bottom: 0,
                itemGap:itemGap,
                itemWidth:itemWidth,
                textStyle:legendStyle
            },
        grid: {
            top:'10%',
            left: '15%',
            right: '18%',
            bottom: '20%'
        },
        xAxis: [
            {
                type: 'category',
                nameTextStyle:textStyle,
                axisPointer: {
                    label: {
                        backgroundColor:backgroundColor,
                        borderColor:borderColor,
                        borderWidth:borderWidth,
                        textStyle:{
                            color:'#3A58A8',
                            fontSize:12,
                            fontFamily:'"DINRegular", sans-serif'
                        },
                        
                        formatter: function (params) {
                            return params.value+"\n"+params.seriesData[0].seriesName+" - "+params.seriesData[0].value+"\n"+params.seriesData[1].seriesName+" - "+parseFloat(params.seriesData[1].value).toFixed(2) +"%";
                        }
                    }
                },
                data: xaxisDate,
                axisLabel:{
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif'
                },
                axisTick:{
                    show:false
                },
                axisLine:{
                    show:false
                }

            }
        ],
           dataZoom: [
            {
                show: false   
            }
        ],
        yAxis: [
            {
                type: 'value',
                show: true,
                name: 'Leads',
                nameLocation: 'middle',
                nameGap:26,
                nameTextStyle:nameTextStyle1,
                axisTick: {
                    alignWithLabel: true
                },
                textStyle:{
                    color:'#000'
                },
                axisLine: {
                    show:false,
                    onZero: false,
                    lineStyle: {
                        color: '#000',
                    }
                },
                
                splitLine:{
                    show:false
                },
                axisTick: {
                    show:false
                },
                axisLabel:{
                    color: '#000',
                    fontFamily:'"DINRegular", sans-serif',
                    fontSize: '@php echo $labelFontSize; @endphp',
                    formatter:function(d)
                    {
                        if(d%1 != 0)
                            return '';
                        else
                            return d;
                    }
                },
                axisPointer:{
                    show:false,
                    label:{
                        backgroundColor:backgroundColor,
                        borderColor:borderColor,
                        borderWidth:borderWidth,
                        textStyle:tooltiptextStyle
                    }
                }
            },
            {
                type: 'value',
                show: true,
                nameTextStyle:nameTextStyle2,
                name: 'Conversion Rate',
                nameLocation: 'middle',
                nameGap:35,
                splitLine:{
                    show:false
                },
                axisTick: {
                    show:false
                },
                axisLine: {
                    show:false,
                    onZero: false,
                    lineStyle: {
                        color: '#000',
                    }
                },
                axisLabel: {
                    formatter: '{value}%',
                    color:'#000',
                    fontSize: '@php echo $labelFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif'
                    
                },
                axisPointer:{
                    show:false,
                    label:{
                        backgroundColor:backgroundColor,
                        borderColor:borderColor,
                        borderWidth:borderWidth,
                        textStyle:tooltiptextStyle
                    }
                }
            }
        ],
    
        series: [
            {
                name: 'Leads',
                type: 'line',
                yAxisIndex: 0,
                smooth: true,
                data: leads,
                splitNumber:4,
                interval:1,
                symbolSize: 3,
                symbol:'circle',
                lineStyle: {
                    
                    width: 1,
                    type: 'dotted',
                  
                },
                itemStyle: {
                    color: colors[0]
                },
                
            },
            {
                name: 'Conversion Rate',
                type: 'line',
                yAxisIndex: 1,
                smooth: true,
                data: rate,
                symbolSize: 3,
                symbol:'circle',
                lineStyle: {
                    width: 1,
                    type: 'dotted'
                },
                itemStyle: {
                    color: colors[1]
                },
            }
        ]
    };
    chart.setOption(option);
    }
</script>
@endpush
