
<div class="dashboard-box">
<span class="dashboard-spiner-icon" id ='lead-count-by-verification-method-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Verification Method by Channel<span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "lead-count-by-verification-method"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <div class="row">
    <div class="col-md-12" style="margin-left:-10px;">
            <div class="" id="lead-count-by-verification-method" style="width: 100%; height: 159px;"></div>
        </div>
        <div class="col-md-12 mt10 ">
            <div class="checkbox-echart">
                <div class="col-md-1 col-xs-1 col-sm-1"></div>
                <div class="col-md-5 col-xs-5 col-sm-5">
                    <label style="color: black; padding-left: 5px; font-family: 'DINRegular', sans-serif;">Door-to-Door</label> 
                    <label class="checkbx-style" style="font-size:10px;line-height:13px;font-family: 'DINRegular', sans-serif;">
                        <input autocomplete="off" type="radio" name="agent-lead-type" id="d2d-good" class="agent-verification-method">Good Sales
                            <span class="checkmark checkbox-type" style="width:13px;height:13px;font-size:10px;"></span>
                    </label>
                    <label class="checkbx-style" style="font-size:10px;line-height:13px; font-family: 'DINRegular', sans-serif;"> 
                        <input autocomplete="off" type="radio" name="agent-lead-type" id="d2d-bad" class="agent-verification-method">Bad Sales
                            <span class="checkmark checkbox-type" style="width:13px;height:13px;font-size:10px;"></span>
                    </label>
                </div>
                <div class="col-md-5 col-xs-5 col-sm-5" >
                    <label style="color: black; padding-left: 5px;font-family: 'DINRegular', sans-serif;">Telemarketing</label> 
                    <label class="checkbx-style" style="font-size:10px;line-height:13px;font-family: 'DINRegular', sans-serif;"> 
                        <input autocomplete="off" type="radio" name="agent-lead-type" id="tele-good" class="agent-verification-method">Good Sales
                            <span class="checkmark checkbox-type" style="width:13px;height:13px;font-size:10px;"></span>
                    </label>
                    <label class="checkbx-style" style="font-size:10px;line-height:13px;font-family: 'DINRegular', sans-serif;"> 
                        <input autocomplete="off" type="radio" name="agent-lead-type" id="tele-bad" class="agent-verification-method">Bad Sales
                            <span class="checkmark checkbox-type" style="width:13px;height:13px;font-size:10px;"></span>
                    </label>
                </div>
            </div>
        </div>
        
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function(){
    $('.agent-verification-method').click(function(){
        $('#lead-count-by-verification-method-loading').css('visibility','visible');
        $('.agent-verification-method').css('background-color','#fff');
        $(this).css('background-color','#000');
        attr = $(this).attr('id').split('-');
        $('.hidden-agent-type').attr('value',attr[0]);
        if(attr[1] == 'good')
        {
            $('.hidden-agent-lead-type').attr('value','Good Sale');
        }
        else
        {
            $('.hidden-agent-lead-type').attr('value','Bad Sale');
        }
        var data = $("#deshbordNewForm").serializeArray();
        loadLeadsByVerificationMethodChartData(data);
    });
});
function loadLeadsByVerificationMethodChartData(data)
{

    $.ajax({
        url: '{{route("dashboard.leads.count.verification.method")}}',
        method:'post',
        data: data,
        success:function(data)
        {   
            if(data.status == 'success')
            {
                $('#lead-count-by-verification-method-loading').css('visibility','hidden');
                if (data.data.agentVerificationMethodData.length <= 0) {
                    document.getElementById("lead-count-by-verification-method").innerHTML = "";
                    $("#lead-count-by-verification-method").removeAttr("_echarts_instance_");
                    return false;
                 }
                else{
                    
                    agentVerificationMethodData = data.data.agentVerificationMethodData;
                    loadverificationMethodDonutChart("lead-count-by-verification-method",agentVerificationMethodData);
                }
            }
        }
    });
    
}

function loadverificationMethodDonutChart(chartId, data){

    var chart = echarts.init(document.getElementById(chartId));
        var salesCenterId;
        var salesCenterNames = [];
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
        $.each(data, function( key, value ) {
            
            switch(value["name"]) {
                case "Customer Inbound":
                    data[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[0]}}',
                    }
                    break;
                case "Agent Inbound":
                    data[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[1]}}',
                    }
                    break;
                case "Email":
                    data[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[2]}}',
                    }
                    break;
                case "Text":
                    data[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[3]}}',
                    }
                    break;
                case "IVR Inbound":
                    data[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[4]}}',
                    }
                    break;
                case "TPV Now Outbound":
                    data[key]['itemStyle'] = {
                        color: '{{explode(',',$colors)[5]}}',
                    }
                    break;
            }
        });
        
        chart.setOption({
            tooltip: {
                trigger: 'item',
                @if($identifier == 'mobile')
                    position: [10, -100],   
                @endif
                backgroundColor:backgroundColor,
                borderColor:borderColor,
                borderWidth:borderWidth,
                textStyle:{
                    color:'#3A58A8',
                    fontSize:12,
                    fontFamily:'"DINRegular", sans-serif'
                },
                formatter: function(x) {
                    
                    return x.seriesName+ "<br/>"+x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
            },
            calculable: true,
            series: [{
                name: 'Verification Method',
                type: 'pie',
                radius: '55%',
                avoidLabelOverlap: true,
                rotate:true,
                selectedMode: 'single',
                selectedOffset: 5,
                hoverOffset:hoverOffset,
                rotate: 45,
                center:['50%','65%'],
                top:0,
                // color:colorArray,
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
                    fontSize:'@php echo $countFontSize; @endphp',
                    fontFamily:'"DINRegular", sans-serif',
                    formatter: function(x) {
                        x.name = x.name.replace(' ','\n');
                    return  x.name+'\n' + parseFloat(x.percent).toFixed(2)+ '%';
                },
                    rotate: 0,
                },
                data: data,//.sort(function (a, b) { return a.value - b.value; }),
                // roseType: 'radius',
                // animationType: 'scale',
            // animationEasing: 'elasticOut',
            }]

        });
        chart.off('click');
        chart.on('click', function(params) {
            
            $('#telesales-status-leads-modal .modal-title').html(params.data.name +' Report');
            
            $('.charthiddenfield #verificationMethod').val(params.data.name);
            type = $('.hidden-agent-type').attr('value');
            status = $('.hidden-agent-lead-type').attr('value');
            $('.charthiddenfield #status').val(status);
            $('.charthiddenfield #channelType').val(type);
            $('.charthiddenfield #sheet_name').val(status+" Report");
            $('.charthiddenfield #sheet_title').val(params.name+" "+status+" Report");
            $('.charthiddenfield #brand').val($('.hidden-brand').attr('value'));
            getTelesalesLeadsByStatus(status, '','',type, '', params.data.name, '','');
        });
    }
</script>
@endpush
