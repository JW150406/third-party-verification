<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='leads-by-state-map-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Leads by State<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "leads-by-state-map"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    
    <div id="leads-by-state-map" style="width: 100%; height:220px;"></div>
</div>

@push('scripts')
<script>
function loadLeadsByStateMapData(data)
{

    $.ajax({
        url: '{{route("dashboard.load.state.wise.lead.map")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            if(data.status == 'success')
            {
                $('#leads-by-state-map-loading').css('visibility','hidden');
                loadLeadsByStateMap("leads-by-state-map",data.data);
            }
        }
    });

}

function loadLeadsByStateMap(chartId, data) {
    var myChart = echarts.init(document.getElementById(chartId));
// Specify configurations and data graphs 
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
    myChart.showLoading();

    $.get('https://s3-us-west-2.amazonaws.com/s.cdpn.io/95368/USA_geo.json', function (usaJson) {
    myChart.hideLoading();

    echarts.registerMap('USA', usaJson, {
    Alaska: {              // 把阿拉斯加移到美国主大陆左下方
        left: -131,
        top: 25,
        width: 15
    },
    Hawaii: {
        left: -110,        // 夏威夷
        top: 28,
        width: 5
    },
    'Puerto Rico': {       // 波多黎各
        left: -76,
        top: 26,
        width: 2
    }
    });
    option = {

    tooltip : {
        trigger: 'item',
        showDelay: 0,
        transitionDuration: 0.2,
        backgroundColor:backgroundColor,
        borderColor:borderColor,
        borderWidth:borderWidth,
        textStyle:tooltiptextStyle,
        formatter : function (params) {
            if(isNaN(params.value) == true)
                value = 0;
            else
            value = params.value;
            return params.name + ' : ' + value;
        }
    },
    visualMap: {
        bottom:0,
        left: 'right',
        min: 0,
        max: data.max,
        color: ['{{explode(',',$colors)[1]}}','{{explode(',',$colors)[2]}}','{{explode(',',$colors)[3]}}','#e9e9c5',],//['#235175', '#4E94CA','#C3DBED'],
        text:['High','Low'],
        calculable : true,
        itemWidth:10,
        itemHeight:50,
        textStyle:textStyle
        
    },
    series : [
        {
            type: 'map',
            roam: true,
            map: 'USA',
            zoom:1.25,
            itemStyle:{
                emphasis:{
                    label:{show:true}
                },
            },
            textFixed : {
                Alaska : [30, -30]
            },
            highlightColor:'#000',
            data:data.data
        }
    ]
    };

    myChart.setOption(option);
    myChart.off('click');
    myChart.on('click', function(params) {
        if(isNaN(params.value) != true)
        {
            $('#telesales-status-leads-modal .modal-title').html(params.name +' Report');
            $('.charthiddenfield #status').val(params.name);
            locationId = $('.hidden-sales-location-id').attr('value');
            $('.charthiddenfield #location_id').val(locationId);
            salesCenter = $('.hidden-salescenter').val();
            $('.charthiddenfield #sales_center_id').val(salesCenter);
            $('.charthiddenfield #state').val(params.data.id);
            $('.charthiddenfield #sheet_name').val(params.name+" Report");
            $('.charthiddenfield #sheet_title').val(params.name+" Report");
            $('.charthiddenfield #brand').val($('.hidden-brand').attr('value'));
            getTelesalesLeadsByStatus('', '',salesCenter,'', '', '', '',locationId,'','','','','','',params.data.id);
        }
                
    });
});


}
</script>
@endpush
