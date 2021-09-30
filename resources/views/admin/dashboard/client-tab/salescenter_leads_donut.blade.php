<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='salescenter-leads-donut-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by Sales Center<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "salescenter-leads-donut"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div id="salescenter-leads-donut" style="width: 100%; height:217px;"></div>
    <input type="hidden" class="echartinst" value>
    
</div>

@push('scripts')
<script>
$(document).ready(function(){
    var chart = echarts.init(document.getElementById('salescenter-leads-donut'));
})
function loadSalesCenterDonutChartData(data)
{
    $.ajax({
        url: '{{route("dashboard.load.salescenter.donut")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                
                $('#salescenter-leads-donut-loading').css('visibility','hidden');
                salesCenterData = jQuery.parseJSON(data.data.salesCenterData);
                loadSalesCenterDonutChart("salescenter-leads-donut",data.data.salesCenterNames,salesCenterData);
            }
        }
    });

}

function loadSalesCenterDonutChart(chartId, statusList, statusData) {
        var chart = echarts.init(document.getElementById(chartId));
        var salesCenterId;
        var salesCenterNames = [];
        var radius = [40, 55];
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
        chart.setOption({
            tooltip: {
                trigger: 'item',
                @if($identifier == 'mobile')
                    position: [10, -100],   
                @endif
                backgroundColor:backgroundColor,
                borderColor:borderColor,
                borderWidth:borderWidth,
                textStyle:tooltiptextStyle,
                
                formatter: function(x) {
                        salesCenter = x.name.split('-');
                        salesCenterId = salesCenter[1];
                        $('.hidden-salescenter-id').attr('value',salesCenterId);
                        $('.hidden-sales-location-id').attr('value',"");
                        $('.hidden-agent-id').attr('value', "");
                        var data = $("#deshbordNewForm").serializeArray();
                        var tooltip;
                        let chartId = "leads-by-sales-center-"+salesCenterId;
                        div = document.getElementById(chartId);
                        if(div == null)
                        {   
                            var newDiv = document.createElement("div"); 
                            newDiv.setAttribute("id",chartId);
                            newDiv.style.width  ='300px';
                            newDiv.style.height = '217px';
                            parentDiv = document.getElementsByClassName('tooltip-chart-container')[0];
                            parentDiv.appendChild(newDiv);  
                            loadSalesCenterpieChartData(data,chartId,salesCenter[0]);
                        }
                        chart =  echarts.init(document.getElementById(chartId));
                        let src = chart.getConnectedDataURL({
                            type:'png',
                            pixelRatio: 1,
                            backgroundColor: '#fff'
                        });
                        tooltip = "<img src='"+src+"'>";
                    // return x.seriesName+ "<br/>"+salesCenter[0]+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                    return tooltip;
                }
            },
            legend: {
                formatter:function(x)
                {
                    salesCenter = x.split('-');
                    return salesCenter[0];
                },
                itemGap:itemGap,
                itemWidth: itemWidth,
                orient: 'horizontal',
                bottom: 0,
                data: statusList,
                label: "Sales Center",
                type: 'scroll',
                icon : '@php echo $legendIcon; @endphp',
                fontSize: '@php echo $labelFontSize; @endphp',
                textStyle: legendStyle,
                type:'scroll',
                pageIcons:{
                    horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                }, 
                pageIconSize:pageIconSize,
                pageFormatter: pageFormatter,
                pageButtonItemGap:pageButtonItemGap
            },
            calculable: true,
            series: [{
                name: 'Sales Center',
                type: 'pie',
                radius: ['40%', '55%'],
                avoidLabelOverlap: true,
                fontFamily:'"DINRegular", sans-serif',
                top:'0%',
                bottom:'0%',
                center:['50%','45%'],
                hoverOffset:hoverOffset,
                color: @php echo json_encode($colors) @endphp,//@php echo json_encode(colorArray()); @endphp,
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
                    formatter: function(x) {
                    return  x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                },
                    rotate: 0,
                },
                data: statusData
            }]

        },false);
        chart.off('click');
        chart.on('click', function(params) {
            // $('.hidden-salescenter-id').attr('value',salesCenterId);
            // $('.hidden-sales-location-id').attr('value',"");
            // $('.hidden-agent-id').attr('value', "");
            // var data = $("#deshbordNewForm").serializeArray();
            // $('.salescenter-name').html(params.name.split('-')[0]+'<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId ="salescenter-leads-details-pie"><i class="fa fa-download" aria-hidden="true"></i></a></span>');
            // loadSalesCenterpieChartData(data);

            $('#telesales-status-leads-modal .modal-title').html(params.name.split('-')[0] +' Verification Status by  Commodity');
            $('.charthiddenfield #status').val(params.name.split('-')[0]);
            salesCenterId = $('.hidden-salescenter-id').attr('value');
            $('.charthiddenfield #sales_center_id').val(salesCenterId);
            $('.charthiddenfield #sheet_name').val(params.name.split('-')[0]+" Report");
            $('.charthiddenfield #sheet_title').val(params.name.split('-')[0]+" Report");
            getTelesalesLeadsByStatus('', '',salesCenterId, '', '', '', '','');
        });
        // chart.on('legendselectchanged', function(params) {
        //     chart.dispatchAction({
        //      type: 'legendAllSelect',
        //     });   
        //     $('#telesales-status-leads-modal .modal-title').html(params.name.split('-')[0] +' Verification Status by  Commodity');
        //     // $('.charthiddenfield #status').val(params.name);
        //     // salesCenterId = $('.hidden-salescenter-id').attr('value');
        //     $('.charthiddenfield #sales_center_id').val(params.name.split('-')[1]);
        //     getTelesalesLeadsByStatus('', '',params.name.split('-')[1], '', '', '', '','');
        // });
    }
</script>
@endpush
