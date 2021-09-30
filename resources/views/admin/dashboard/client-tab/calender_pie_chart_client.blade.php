<div class="dashboard-box monthly-sum">
<span class="dashboard-spiner-icon" id ='calender-pie-chart-clients-loading'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></span><h4 class="dash-hd-title">Monthly Summary <span><a href="javascript:void(0)" style="float:right;" class="downloadImage" divId = "calender-pie-chart-clients"><i class="fa fa-download" aria-hidden="true"></i></a></span></h4>
    <?php
        if(Auth::check())
            $timezone = Auth::user()->timezone;
        else
            $timezone = getClientSpecificTimeZone();
        ?>
    <div class="row calender-month-filter">
   
    <div class="col-xs-1 col-sm-1 col-md-3 col-lg-3 text-right"><a href="javascript:void(0)" class="theme-color" id="datepicker-arrow-left"><i class="datepicker-arrow fa fa-chevron-left"  attr = "left"aria-hidden="true"></i></a></div>
        <div class="col-xs-5 col-sm-5 col-md-3 col-lg-3" style="margin-bottom:0px;">
        
        <select class="select2 form-control month-filter calender-submit" id="month-select"  name="monthFilter" style="margin:0; border-bottom: 1px solid #d6d6d6;">
                @for($i=0 ;$i < 12 ;$i++)
                    @if(Carbon\Carbon::now()->setTimezone($timezone)->format('m') == Carbon\Carbon::parse(Carbon\Carbon::now()->setTimezone($timezone)->format('Y').'-01')->addMonth($i)->format('m'))
                        <option value="{{Carbon\Carbon::parse(Carbon\Carbon::now()->setTimezone($timezone)->format('Y').'-01')->addMonth($i)->format('m')}}" selected>{{Carbon\Carbon::parse(Carbon\Carbon::now()->setTimezone($timezone)->format('Y').'-01')->addMonth($i)->format('M')}}</option>
                    @else
                        <option value="{{Carbon\Carbon::parse(Carbon\Carbon::now()->setTimezone($timezone)->format('Y').'-01')->addMonth($i)->format('m')}}">{{Carbon\Carbon::parse(Carbon\Carbon::now()->setTimezone($timezone)->format('Y').'-01')->addMonth($i)->format('M')}}</option>
                    @endif
                    @endfor
            </select>
        </div>
        <div class="col-xs-5 col-sm-5 col-md-3 col-lg-3">
            <select class="select2 form-control year-filter calender-submit" name="yearFilter" style="margin:0; border-bottom: 1px solid #d6d6d6;">
            <option value="{{Carbon\Carbon::now()->setTimezone($timezone)->format('Y')}}" selected>{{Carbon\Carbon::now()->setTimezone($timezone)->format('Y')}}</option>
                @for($i=1 ;$i < 20 ;$i++)
                
                <option value="{{Carbon\Carbon::now()->setTimezone($timezone)->subYear($i)->format('Y')}}">{{Carbon\Carbon::now()->setTimezone($timezone)->subYear($i)->format('Y')}}</option>
                @endfor

            </select>
            
        </div>
        <div class="col-xs-1 col-sm-1 col-md-3 col-lg-3"> <a href="javascript:void(0)"class="theme-color" id="datepicker-arrow-right"><i class="datepicker-arrow fa fa-chevron-right" attr="right" aria-hidden="true"></i></a></div>
        
    </div>

    <div id="calender-pie-chart-clients" style="width: 100%; height: 184px"></div>
</div>

@push('scripts')
<style>
.month-filter .select2-container .select2-choice .select2-arrow b:before {
    content : '';
}
.year-filter .select2-container .select2-choice .select2-arrow b:before {
    content : '';
}
.datepicker-arrow{
    margin:9px 0 0 0;
}
</style>
<script>

    $(".calender-submit").select2({
        minimumResultsForSearch: -1
    });
    function loadCalenderPieChartData(data)
    {
      $.ajax({
        url: '{{route("dashboard.calender.pie.client.data")}}',
        method:'post',
        data:data,
        success:function(data)
        {   
            if(data.status == 'success')
            {
                $('#calender-pie-chart-clients-loading').css('visibility','hidden');
                clientData = data.data.clientData;
                loadCalenderPieClientChart("calender-pie-chart-clients",data.data.status,clientData,data.data.startDate.date,data.data.endDate.date,data.data.range);

            }
        }
    });
    
}

function loadCalenderPieClientChart(chartId,status,data,startDate,endDate,range){
    var cellSize = [38, 38];
    myChart = echarts.init(document.getElementById(chartId));
    var pieRadius = 11;
    <?php      
        if($identifier == 'mobile')
        {
            $calenderColors = implode(',',calenderColorArray());    
        }
        else
        {
            if(isset($calenderColors))
            {
                $calenderColors = implode(',',calenderColorArray());    
            }
            else
            {
                $calenderColors = implode(',',calenderColorArray());    
            }
        }
        ?>
    $.each(data, function( key, value ) {
        $.each(value,function(k,v){

            switch(v["name"]) {
                case "Good Sale":
                    data[key][k]['itemStyle'] = {
                        color: '{{explode(',',$calenderColors)[0]}}',  //72DFDB  88A96A  B2CFE6
                    }
                    break;
                case "Pending Leads":
                    data[key][k]['itemStyle'] = {
                        color: '{{explode(',',$calenderColors)[1]}}',
                    }
                    break;
                case "Bad Sale":
                    data[key][k]['itemStyle'] = {
                        color: '{{explode(',',$calenderColors)[2]}}',
                    }
                    break;
                case "Cancelled Leads":
                    data[key][k]['itemStyle'] = {
                        color: '{{explode(',',$calenderColors)[3]}}',
                    }
                    break;
            }
        })
        });
function getVirtulData() {
        var date = +echarts.number.parseDate(startDate);
    var end = +echarts.number.parseDate(endDate);
    var dayTime = 3600 * 24 * 1000;
    var data = [];
    for (var time = date; time < end; time += dayTime) {
        data.push([
            echarts.format.formatTime('yyyy-MM-dd', time),
            Math.floor(Math.random() * 10000)
        ]);
    }
    return data;
}

function getPieSeries(scatterData, chart) {
    return echarts.util.map(scatterData, function (item, index) {
        var center = chart.convertToPixel('calendar', item);
        return {
            id: index + 'pie',
            type: 'pie',
            center: center,
            hoverOffset:hoverOffset,
            color: @php echo json_encode(colorArray()); @endphp,
            label: {
                normal: {
                    formatter: '{c}',
                    position: 'inside',
                    fontSize: 7,
                    fontFamily:'"DINRegular", sans-serif',
                    fontWeight: 1000
                }
            },
            radius: pieRadius,
            data: data[index]
        };
    });
}

function getPieSeriesUpdate(scatterData, chart) {
    return echarts.util.map(scatterData, function (item, index) {
        var center = chart.convertToPixel('calendar', item);
        return {
            id: index + 'pie',  
            center: center
        };
    });
}

var scatterData = getVirtulData();

option = {
    tooltip : {
        backgroundColor:backgroundColor,
        borderColor:borderColor,
        borderWidth:borderWidth,
        textStyle:{
            color:'#3A58A8',
            fontSize:12,
            fontFamily:'"DINRegular", sans-serif'
        }   
        
    },
    legend: {
        data: status,
        bottom: 0,
        itemGap:itemGap,
        itemWidth:itemWidth,
        icon: '@php echo $legendIcon;@endphp',
        textStyle:legendStyle,
        type:'scroll',
        pageIcons:{
            horizontal:[legendScrollLeftIcon,legendScrollRightIcon]
        }, 
        pageIconSize:pageIconSize,
        pageFormatter: pageFormatter,
        pageButtonItemGap:pageButtonItemGap
    },
    calendar: {
        top: '4%',
        left: 'center',
        bottom:'10%',
        orient: 'vertical',
        cellSize: cellSize,
        yearLabel: {
            show: true,
            textStyle: {
                fontSize: 8
            }
        },
        dayLabel: {
            show:false,
            // margin: 20,
            firstDay: 0,
            nameMap: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
        },
        monthLabel: {
            show: true,
            fontFamily:'"DINRegular", sans-serif'
        },
        range: range
    },
    series: [{
        id: 'label',
        type: 'scatter',
        coordinateSystem: 'calendar',
        symbolSize: 1,
        symbolOffset:[0,0],
        color:'#fff',

        label: {
            normal: {
                show: true,
                formatter: function (params) {
                    return echarts.format.formatTime('dd', params.value[0]);
                },
                offset: [-cellSize[0] / 2 + 7, -cellSize[1] / 2 + 10],
                textStyle: {
                    color: '#000',
                    fontWeight: 'bold',
                    fontSize: 8,
                }
                }
            },
                data: scatterData
            }]
        };

        var pieInitialized;
        setTimeout(function() {
            pieInitialized = true;
            myChart.setOption({
                series: getPieSeries(scatterData, myChart)
            });
        }, 10);

        window.onresize = function() {
            if (pieInitialized) {
                myChart.setOption({
                    series: getPieSeriesUpdate(scatterData, myChart)
                });
            }
        };

        myChart.setOption(option);
        myChart.off('click');
        myChart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.name + ' Report');
            $('.charthiddenfield #status').val(params.name);
            $('.charthiddenfield #calender_day').val(params.seriesIndex);
            calenderDay = params.seriesIndex;
            // alert(calenderDay);
            month = $('.month-filter').children("option:selected").val();
            year = $('.year-filter').children("option:selected").val();
            $('.charthiddenfield #month').val(month);
            $('.charthiddenfield #year').val(year);
            locationId = $('.hidden-sales-location-id').val();
            $('.charthiddenfield #location_id').val(locationId);
            salesCenter = $('.hidden-salescenter').val();
            $('.charthiddenfield #sales_center_id').val(salesCenter);
            $('.charthiddenfield #sheet_name').val(params.name+" Report");
            $('.charthiddenfield #sheet_title').val(params.name+" Report");
            
            getTelesalesLeadsByStatus(params.name, '',salesCenter, '', '', '', '',locationId,calenderDay,month,year);
        });
    }
    $(document).ready(function(){
        month = $('.month-filter').select2('data')[0]['id'];
            year1 = $('.year-filter').select2('data')[0]['id'];
            todayDate = moment().format('YYYY MM');
            
            todayMonth = todayDate.split(' ')[1];
            todayYear = todayDate.split(' ')[0];
            if(todayMonth == month && todayYear == year1)
                {
                    $('#datepicker-arrow-right').addClass('cursor-none');
                    // $('#datepicker-arrow-right').css('disabled','disabled');
                    // $('#datepicker-arrow-right').removeAttr('href');
                }
        $('.datepicker-arrow').on('click',function(){
            month = $('.month-filter').select2('data')[0]['id'];
            year1 = $('.year-filter').select2('data')[0]['id'];
            // todayDate = new Date();
            todayDate = moment().format('YYYY MM');
            
            todayMonth = todayDate.split(' ')[1];
            todayYear = todayDate.split(' ')[0];
            subtractYearNow = moment(new Date());
            subtractYear = subtractYearNow.subtract(19, 'years').format('YYYY');
            let now = moment(new Date(year1,parseInt(month)-1,1));
            let monthYear;
            $('#datepicker-arrow-right').removeClass('cursor-none');
            if($(this).attr('attr') == 'left')
            {
                if(month <= '01' && subtractYear == year1)
                {
                    $('#datepicker-arrow-left').addClass('cursor-none');
                    return false;
                }else{
                    $('#datepicker-arrow-left').removeClass('cursor-none');
                }
                monthYear = now.subtract(1, 'months').format('YYYY MM DD');   
            }
            else if($(this).attr('attr') == 'right')
            {
                if(todayMonth <= month && todayYear <= year1)
                {
                    $('#datepicker-arrow-right').addClass('cursor-none');
                    return false;
                }
                else
                {
                    $('#datepicker-arrow-right').removeClass('cursor-none');
                }
                monthYear = now.add(1, 'months').format('YYYY MM DD');
            }
            dateMonth = (parseInt(monthYear.split(" ")[1])).toString().padStart(2, "0");
            dateYear = monthYear.split(" ")[0];

            $('.month-filter').val(dateMonth);
            $('.month-filter').trigger('change');
            $('.year-filter').val(dateYear).trigger('change');
            $('.year-filter').trigger('change');
            yearText = $('.year-filter').select2('data')['text'];   
            $('#select2-month-select-container').text(getMonth(dateMonth));
            $('.year-filter #select2-chosen-3').html(yearText);

        });
    })  
    function getMonth(value)
    {
        let month;
        switch(value)
        {   
            case '01':
                month = 'Jan';
                break;
                case '02':
                month = 'Feb';
                break;
                case '03':
                month = 'Mar';
                break;
                case '04':
                month = 'Apr';
                break;
                case '05':
                month = 'May';
                break;
                case '06':
                month = 'Jun';
                break;
                case '07':
                month = 'Jul';
                break;
                case '08':
                month = 'Aug';
                break;
                case '09':
                month = 'Sep';
                break;
                case '10':
                month = 'Oct';
                break;
                case '11':
               month = 'Nov';
                break;
            case '12':
                month = 'Dec';
                break;
        }
        return month;
    }
 </script>
 @endpush
