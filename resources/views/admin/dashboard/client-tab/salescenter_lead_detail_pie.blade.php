
<!-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 300px;">
    <div class="modal-content">
      <div class="dashboard-box">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="dash-hd-title salescenter-name">Pie Chart<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId ="salescenter-leads-details-pie"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
          <div class="row" style="text-align: right; margin-right: -7px;"><a id="export-tooltip"><i class="fa fa-eye" style="color: #1c5997;"></i></a></div>
        <div id="salescenter-leads-details-pie" style="width: 100%; height: 230px"></div>
        </div>
      
    </div>
  </div>
</div> -->
<div class="tooltip-chart-container" style="visibility:hidden;position:fixed;left:0;top:0;z-index:-10000;">
</div>
@push('scripts')
<script>
    let isExportModalOpen = false;
function loadSalesCenterpieChartData(data,chartId,chartTitle)
{
 
    $.ajax({
        url: '{{route("dashboard.load.salescenter.pie.data")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                salesCenterData = jQuery.parseJSON(data.data.salesCenterData);
                loadSalesCenterPieChart(chartId,data.data.salesCenterNames,salesCenterData,chartTitle);
            }
        }
    });
    
}

function loadSalesCenterPieChart(chartId, statusList, statusData,chartTitle) {
        var chart = echarts.init(document.getElementById(chartId));
        <?php
        if($identifier == 'mobile')
        {
            $colors = implode(',',colorArray());    
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
                $colors = implode(',',colorArray());
            }
        }
        ?>
        $.each(statusData, function( key, value ) {
            switch(value["name"]) {
                case "Good Sale":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[0]}}',
                    }
                    break;
                case "Pending Leads":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[1]}}',
                    }
                    break;
                case "Bad Sale":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[2]}}',
                    }
                    break;
                case "Cancelled Leads":
                    statusData[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[3]}}',
                    }
                    break;
            }
        });

        var radius = [40, 55];
        chart.setOption({
            tooltip: {
                trigger: 'item',
                confine: true,
                formatter: function(x) {
                    return x.seriesName+ "<br/>"+x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
            },
            title: {
                text: chartTitle,
                left: "center",
                textStyle: {
                fontSize: 12,
                fontFamily:'"DINRegular", sans-serif'
                },
            },
            legend: {
                itemGap:itemGap,
                itemWidth: itemWidth,
                orient: 'horizontal',
                bottom: 0,
                data: statusList,
                label: "Sales Center",
                type: 'scroll',
                icon : '@php echo $legendIcon; @endphp',
                textStyle:legendStyle,
                pageIcons:{
                    horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
                }, 
                pageIconSize:pageIconSize,
                pageFormatter: pageFormatter,
                pageButtonItemGap:pageButtonItemGap
                
                
            },
            calculable: true,
            series: [{
                name: 'Status',
                type: 'pie',
                radius: ['35%', '50%'],
                avoidLabelOverlap: true,
                selectedMode: 'single',
                selectedOffset: 5,
                animation:false,
                rotate:true,
                hoverOffset:hoverOffset,
                labelLine: {
                    lineStyle: {
                        color: 'rgba(0, 0, 0, 1)'
                    }
                },
                rotate: 45,
                top:0,
                color: @php echo json_encode($colors); @endphp,
                label: {
                    show: true,
                    position: "outside",
                    rotate: true,
                    fontSize: '@php echo $countFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif',
                    color:color,
                    formatter: function(x) {
                    return  x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                },
                labelLine: {
                    lineStyle: {
                        color: 'rgba(0, 0, 0, 1)'
                    },
                },
                    rotate: 0,
                },
                data: statusData,
            animationEasing: 'elasticOut',
            }]

        });
        // return document.getElementById(chartId).innerHTML;
        // $('#exampleModal').modal();
        // chart.on('click', function(params) {
        //     $('#telesales-status-leads-modal .modal-title').html(params.seriesName +' Verification Status by  Commodity');
        //     $('.charthiddenfield #status').val(params.name);
        //
        //     salesCenterId = $('.hidden-salescenter-id').attr('value');
        //     agentId = $('.hidden-agent-id').attr('value');
        //     locationId = $('.hidden-sales-location-id').attr('value');
        //     $('.charthiddenfield #sales_center_id').val(salesCenterId);
        //     $('.charthiddenfield #agent_id').val(agentId);
        //     $('.charthiddenfield #location_id').val(locationId);
        //     getTelesalesLeadsByStatus(params.name, agentId,salesCenterId, '', '', '', '',locationId);
        // });
        // chart.dispatchAction({
        //     type: 'legendSelect'
        // });   
    }

    $('#exampleModal').on('shown.bs.modal', function (e) {
        var resizeChartId = $("#salescenter-leads-details-pie").attr('_echarts_instance_');
        chartId = echarts.init(document.getElementById("salescenter-leads-details-pie"));
        window.echarts.getInstanceById(resizeChartId).resize(); 
        chartId.dispatchAction({
            type: 'legendAllSelect'
        });
    });

    $('body').on('click', '#export-tooltip', function() {
        $('#exampleModal').modal('hide');
        isExportModalOpen = true;
    });

    $('#exampleModal').on('hidden.bs.modal', function () {
               
        if (isExportModalOpen == true) {
            salesCenterId = $('.hidden-salescenter-id').attr('value');
            agentId = $('.hidden-agent-id').attr('value');
            locationId = $('.hidden-sales-location-id').attr('value');
            $('.charthiddenfield #sales_center_id').val(salesCenterId);
            $('.charthiddenfield #agent_id').val(agentId);
            $('.charthiddenfield #location_id').val(locationId);
            let viewName = $("#exampleModal .modal-dialog .salescenter-name").html();
            viewName  =viewName.split('<span>')[0];
            $('.charthiddenfield #sheet_name').val(viewName+" Leads Report");
            $('.charthiddenfield #sheet_title').val(viewName+" Leads Report");
            $('#telesales-status-leads-modal .modal-title').html(viewName + " leads report");
            getTelesalesLeadsByStatus(viewName,agentId,salesCenterId, '', '', '', '',locationId);
        }
        else
        {
            $('.hidden-sales-location-id').attr('value',''); 
        }
    });
</script>
@endpush