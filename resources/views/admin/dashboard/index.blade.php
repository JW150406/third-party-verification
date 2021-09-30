@extends('layouts.admin')
@section('content')
@php $legendIcon = 'circle';
    $labelFontSize = 10;
    $countFontSize = 9;
    $iconSize = 13;
    
 @endphp
 <script>
    hoverOffset = 2;
    color = '#000';
    itemGap = 2;
    itemWidth = 14,
    barMaxWidth = 30;
    pageIconSize=10;
    pageFormatter= false;
    pageButtonItemGap=-10;
    saveButtonBottom = 12;
    legendScrollLeftIcon = 'image:// {{asset("images/chevron-left.png")}}';
    legendScrollRightIcon = 'image:// {{asset("images/chevron-right.png")}}';
    downloadIcon = 'image:// {{asset("images/download.svg")}}';
    textStyle = {
                    fontWeight:'bold',
                    fontFamily:'"DINRegular", sans-serif',
                    fontSize: '@php echo $labelFontSize; @endphp',
                    color:'#000',
                };
    legendStyle = {
        fontFamily:'"DINRegular", sans-serif',
        fontSize: '@php echo $labelFontSize; @endphp',
        padding:[0,0,0,-2],
        color:'#000',
    };
    backgroundColor = '#fff';
        borderWidth = 1;
        borderColor = "#000";
        tooltiptextStyle = {
                    color:'#3A58A8',
                    fontSize:12,
                    fontFamily:'"DINRegular", sans-serif'
                };
 </script>
<div class="dashboard-bg daseboard-update dashboard-header">
  <div class="container-fluid">
      <div class="row">
        @include('admin.dashboard.tabs', ['user' => $user])
      </div>
    </div>
    
</div>

@if ($type == "")

<div class="dashboard-bg daseboard-update">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12" id="dashboard-warp">
            <div class="row">
                <div class="col-xs-12 col-sm-9 col-md-9">
                    @include('admin.dashboard.client-tab.conversion_rate')
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3">
                    <div class="col-md-12 dash-pad-0">
                        @include('admin.dashboard.client-tab.client_logo', ['height' => 66])
                    </div>
                    <div class="col-md-12 dash-pad-0">
                        @include('admin.dashboard.date_range')
                    </div>
                    <div class="col-md-12 dash-pad-0">
                        @include('admin.dashboard.client-tab.brands-filter-dashboard')
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.salescenter_leads_donut')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.client_lead_detail_pie')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.leads_count_line',['height'=> 200])    
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.top_performer',['height' => 220])
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.verification_method_lead_count')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.calender_pie_chart_client')
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.bottom_performer',['height' => 220])
                
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.salescenter_channel_wise')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.salescenter_commodity_wise')
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    @include('admin.dashboard.client-tab.zipcode_lead_wise_map')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                @include('admin.dashboard.client-tab.salesagent_location_wise_map')
                </div>
            </div>
            <!-- modal -->
            @include('admin.dashboard.client-tab.salescenter_lead_detail_pie')
            <div class='row'>
            @include('admin.dashboard.export-modal')
            </div>
            <!-- <div class='row'>
           {{-- @include('admin.dashboard.chart-color-form')--}}
            </div> -->
        </div>
    </div>
</div>
@elseif($type == "salescenter")
  @if ($sId != "")
    @include('admin.dashboard.sales-centers-subtab', ['identifier' => $identifier])
  @else
    @include('admin.dashboard.sales-centers-tab', ['identifier' => $identifier])
  @endif
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/canvasjs.min.js') }}"></script>
<script src="{{ asset('js/echarts/echarts.min.js') }}"></script>
<script src='https://kit.fontawesome.com/a076d05399.js'></script>
<script>
    function calcWidth() {
        var navwidth = 0;
        var morewidth = $('#menu_ul .more').outerWidth(true);
        $('#menu_ul > li:not(.more)').each(function () {
            navwidth += $(this).outerWidth(true);
        });

        var availablespace = $('.dashboard-submenu').width() - morewidth;

        if (navwidth > availablespace) {
            var lastItem = $('#menu_ul > li:not(.more)').last();
            lastItem.attr('data-width', lastItem.outerWidth(true));
            lastItem.prependTo($('#menu_ul .more ul'));
            $(".overflow li a").addClass("test");
            calcWidth();
        } else {
            var firstMoreElement = $('#menu_ul li.more li').first();
            if (navwidth + firstMoreElement.data('width') < availablespace) {
                firstMoreElement.insertBefore($('#menu_ul .more'));
                $(".overflow li a").removeClass("test");
            }
        }

        if ($('.more li').length > 0) {
            $('.more').css('display', 'block');
        } else {
            $('.more').css('display', 'none');
        }
    }

    $(window).on('resize', function () {
        calcWidth();
    });

    $('.more').click(function () {
        $('.overflow').slideToggle();
    });

    $('body').on('click', '.test', function () {
        selectedSubMenu($(this).data('id'));
    });

    function selectedSubMenu(dataId) {
        var text = $('.overflow a[data-id='+dataId+']').html();
        $(".more a.selected").html(text);
    }

    window.onload = function() {

        $('.dashboard-spiner-icon').css('visibility','visible');
        @if($user->access_level == 'salescenter')
            if("{{route('dashboard',['type'=>base64_encode('salescenter'),'sid'=>base64_encode($user->salescenter_id),'cid'=>base64_encode($user->client_id)])}}" != window.location.href)
            {
                window.location.href = "{{route('dashboard',['type'=>base64_encode('salescenter'),'sid'=>base64_encode($user->salescenter_id),'cid'=>base64_encode($user->client_id)])}}";
            }
        @endif
        $('.hidden-start-date').attr('value',$('#startDate').val());

        $('.hidden-end-date').attr('value',$('#endDate').val());
        $('.hidden-sales-location-id').attr('value', $('.select-location').children("option:selected").val());
        $('.hidden-salescenter').attr('value',@php echo $sId;@endphp);
        $('.hidden-month-filter-value').attr('value',$('.month-filter').children("option:selected").val());
        $('.hidden-year-filter-value').attr('value',$('.year-filter').children("option:selected").val());
        var data = $("#deshbordNewForm").serializeArray();
        $('#d2d-good').prop('checked',true);
        $('#line-day').css('background-color','#000');
        @if ($type == "")
            loadConversionRateData(data);
            loadClientLogo(data);
            loadSalesCenterDonutChartData(data);
            loadClientPieChartData(data);
            loadSalesCenterChannelBarChartData(data);
            // loadStatusByStateData(data);
            loadSalesCenterCommodityBarChartData(data);
            loadLeadsCountLineChartData(data);
            loadLeadsByVerificationMethodChartData(data);
            loadTopPerformersData(data);
            loadBottomPerformersData(data);
            loadMapBasedOnZipcode(data);
            loadMapBasedOnSalesAgent(data);
            loadCalenderPieChartData(data);
            $('.hidden-sales-location-id').attr('value','');

        @elseif($type == "salescenter")
          calcWidth();
          @if ($sId != "")
            loadTopPerformersData(data);
            loadBottomPerformersData(data);
            locationId = $('.select-location:selected').attr('value');
            $('.hidden-salescenter').attr('value',@php echo $sId;@endphp);
            var data = $("#deshbordNewForm").serializeArray();
            loadConversionRateData(data);
            loadMapBasedOnZipcode(data);
            // loadSalesCenterLocationChannelBarChartData(data);
            loadStatusByStateData(data);
            // loadLeadByCommodityBarChartData(data);
            loadLeadsCountLineChartData(data);
            loadLocationsWiseLeadsData(data);
            loadSalesCenterChannelBarChartData(data);
            loadSalesCenterCommodityBarChartData(data);
            selectedSubMenu({{$sId}});
            loadLeadsByStateMapData(data);
            loadMapBasedOnSalesAgent(data);
            loadCalenderPieChartData(data);
            // loadClientLogo(data);
          @else
            loadLeadsBySalesCenterLocationData(data);
            loadLeadStatusBySCLocation(data);
            loadSalesCenterLocationChannelBarChartData(data);
            loadStatusByStateData(data);
            loadLeadByCommodityBarChartData(data);
            // loadMapBasedOnZipcode(data);
            loadClientLogo(data);
            loadProgramsLeadsDonutChartData(data);
            loadProvidersLeadsDonutChartData(data);
            loadLeadsByStateMapData(data);
            $('.hidden-sales-location-id').attr('value','');
        @endif
        @endif

    };
    $(".auto-submit").change(function() {
        $('.tooltip-chart-container').html('');
        $('.dashboard-spiner-icon').css('visibility','visible');
        $('#calender-pie-chart-clients-loading').css('visibility','hidden');
        // $('div[_echarts_instance_]').each(function(){
        //     var id = $(this).attr('id');
        //     // document.getElementById(id).innerHTML = "<h5 class='text-center' style='padding-top:25%;vertical-align:middle;'><i class='fas fa-circle-notch fa-spin' aria-hidden='true '></i></h5>";
        //     $("#"+id).removeAttr("_echarts_instance_");
        // });
        
        $('.hidden-agent-type').attr('value','d2d');
        $('.hidden-agent-lead-type').attr('value','Good Sale');
        $('#d2d-good').prop('checked',true);
        $('.hidden-start-date').attr('value',$('#startDate').val());
        $('.hidden-end-date').attr('value',$('#endDate').val());
        $('.hidden-brand').attr('value',$('#dash-brand-filter').val());
        $('.hidden-sales-location-id').attr('value', $('.select-location').children("option:selected").val());

        var data = $("#deshbordNewForm").serializeArray();
        @if ($type == "")
            loadConversionRateData(data);
            loadClientLogo(data);
            loadSalesCenterDonutChartData(data);
            loadClientPieChartData(data);
            loadSalesCenterChannelBarChartData(data);
            // loadStatusByStateData(data);
            loadSalesCenterCommodityBarChartData(data);
            loadLeadsCountLineChartData(data);
            loadLeadsByVerificationMethodChartData(data);
            loadTopPerformersData(data);
            loadBottomPerformersData(data);
            loadMapBasedOnZipcode(data);
            loadMapBasedOnSalesAgent(data);
            $('#map_zipcode').html("");
            $('#map_salesagent').html("");
            $('.map_alt_text1').css('display','block');
            $('.map_alt_text2').css('display','block');
            $('#map_salesagent').css('height','0');
            $('#map_zipcode').css('height','0');
            loadMapBasedOnSalesAgent(data);
            loadCalenderPieChartData(data);
        @elseif($type == "salescenter")
            @if ($sId != "")
            $('.hidden-salescenter').attr('value',@php echo $sId;@endphp);
            var data = $("#deshbordNewForm").serializeArray();
                loadConversionRateData(data);
                loadMapBasedOnZipcode(data);
                // loadSalesCenterLocationChannelBarChartData(data);
                loadTopPerformersData(data);
                loadBottomPerformersData(data);
                loadStatusByStateData(data);
                // loadLeadByCommodityBarChartData(data);
                loadLeadsCountLineChartData(data);
                loadLocationsWiseLeadsData(data);
                loadSalesCenterChannelBarChartData(data);
                loadSalesCenterCommodityBarChartData(data);
                loadLeadsByStateMapData(data);
                loadMapBasedOnSalesAgent(data);
                loadCalenderPieChartData(data);
                // loadClientLogo(data);
            @else
                loadLeadsBySalesCenterLocationData(data);
                loadLeadStatusBySCLocation(data);
                loadSalesCenterLocationChannelBarChartData(data);
                loadStatusByStateData(data);
                loadLeadByCommodityBarChartData(data);
                // loadMapBasedOnZipcode(data);
                loadProgramsLeadsDonutChartData(data);
                loadProvidersLeadsDonutChartData(data);
                loadLeadsByStateMapData(data);
                loadClientLogo(data);
                // $('#map_zipcode').css('display','none');
                $('#map_salesagent').css('display','none');
                // $('.map_alt_text1').css('display','block');
                $('.map_alt_text2').css('display','block');
                $('#map_salesagent').css('height','0');
                $('#map_zipcode').css('height','0');
            @endif
        @endif
    });
    $('.calender-submit').change(function(){
        $('#calender-pie-chart-clients-loading').css('visibility','visible');
        $('#datepicker-arrow-right').removeClass('cursor-none');
        $('.hidden-month-filter-value').attr('value',$('.month-filter').children("option:selected").val());
        $('.hidden-year-filter-value').attr('value',$('.year-filter').children("option:selected").val());
        var data = $("#deshbordNewForm").serializeArray();
        loadCalenderPieChartData(data);
    });
    $(window).resize(function() {
        $('div[_echarts_instance_]').each(function(){
            var id = $(this).attr('_echarts_instance_');
            window.echarts.getInstanceById(id).resize();
        });
    });
    $('body').on('click','.downloadImage',function(){

        title = $(this).closest('h4').html();
        title = title.substring(0,title.indexOf("<span>"));
        title += "_"+$('.hidden-start-date').attr('value')+"_to_"+$('.hidden-end-date').attr('value');
            var chart = echarts.init(document.getElementById($(this).attr('divId')));
            image = chart.getDataURL({
                    type:'png',
                    pixelRatio: 1,
                    backgroundColor: '#fff'
                });
            var a = document.createElement('a');
                a.href = image;
                a.download = title+".png";
                document.body.appendChild(a);
                a.click();
        })

</script>
@endpush
