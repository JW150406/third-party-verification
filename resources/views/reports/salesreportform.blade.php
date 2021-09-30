@extends('layouts.admin')

@section('content')
<?php
$breadcrum = array();
$breadcrum[] = array('link' => '', 'text' => "Analytics");
breadcrum($breadcrum);
?>


<?php
$request = Request::all();


?>

<div class="tpv-contbx edit-agentinfo">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                        <div class="client-bg-white">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <h1 data-toggle="collapse" href="#ShowFilter">Daily Sales Report</h1>
                                </div>
                                <div class="col-md-4 col-sm-4 report-toggle">
                                    <span data-toggle="collapse" href="#ShowFilter"></span>
                                </div>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!--report generate form -->
                                <div id="ShowFilter" class="panel-collapse collapse in ">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">

                                            <div class="agent-detailform">
                                                <div class="col-xs-12">
                                                    @if (count($errors) > 0)
                                                    <div class="alert alert-danger">
                                                        <strong>Whoops!</strong> There were some problems with
                                                        your input.<br><br>
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    @endif
                                                    <form id="filter-form" role="form" method="get" action="" data-parsley-validate>

                                                        <div class="row">
                                                            <div class="col-sm-2 col-md-2">
                                                                <div class="form-group {{ $errors->has('date_start') ? ' has-error' : '' }}">
                                                                    <label for="date_start">Date Range<sup class="redtext">*</sup></label>
                                                                    <input id="date_start" type="text" class="form-control required" name="date_start" value="{{ old('date_start') }}@if(isset($request['date_start'])){{$request['date_start']}}@endif" placeholder="Date Range" data-parsley-required='true' data-parsley-required-message="Please select date range" data-parsley-trigger="change">
                                                                    @if ($errors->has('date_start'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('date_start') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('client') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Client<sup class="redtext">*</sup></label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="client" name="client" data-parsley-errors-container="#select2-client-report-error-message" data-parsley-required='true' data-parsley-required-message="Please select client" data-parsley-trigger="change">
                                                                            <option value="" selected>Select</option>

                                                                            @if( count($clients) > 0)
                                                                            @foreach($clients as $client)
                                                                            <option value="{{$client->id}}" @if(isset($request['client']) && $request['client']==$client->id ) selected @endif >{{$client->name}}</option>
                                                                            @endforeach
                                                                            @endif
                                                                        </select>
                                                                        <span id="select2-client-report-error-message"></span>
                                                                        @if ($errors->has('client'))
                                                                        <span class="help-block text-danger">
                                                                            <strong>{{ $errors->first('client') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end-col-3-->

                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('sales_center') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Sales Center</label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="sales_center" name="sales_center">
                                                                            <option value="" selected>All</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end-col-3-->

                                                            <div class="col-sm-2 col-md-2">
                                                                <div class="form-group {{ $errors->has('commodity') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Commodity</label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="commodity" name="commodity">
                                                                            <option value="" selected>All</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end-col-3-->

                                                                <div class="col-sm-2 col-md-2">
                                                                    <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                                                                        <label for="salesvendor">Verification
                                                                            Status</label>
                                                                        <div class="update_client_by_location">
                                                                            <select class="select2 no-search selectclientlocations_report"
                                                                                    id="salesvendor" name="status">
                                                                                <option value="all">All</option>
                                                                                <option value="pending" @if(isset($request['status']) && $request['status']=='pending' ) selected @endif >Pending</option>
                                                                                <option value="verified" @if(isset($request['status']) && $request['status']=='verified' ) selected @endif >Verified</option>
                                                                                <option value="decline" @if(isset($request['status']) && $request['status']=='decline' ) selected @endif >Declined</option>
                                                                                <option value="hangup" @if(isset($request['status']) && $request['status']=='hangup' ) selected @endif >Hangup</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end-col-3-->

                                                            <div class="col-sm-12 col-md-12">
                                                                <div class="btnintable bottom_btns pd0 pull-right">
                                                                    <div class="btn-group">
                                                                        <button class="btn btn-green" style="margin-right:0px;" type="submit">
                                                                            Submit
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--report generate form ends-->
                            </div>
                        </div>
                        <!--end--bg-white-area-->
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {


                $("#filter-form").parsley();

                $("#date_start").on('change', function() {
                    $("input[name=date_start]").parsley().validate();
                });

                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();

                var today = mm + '/' + dd + '/' + yyyy;
                var now = moment(today).subtract(1, 'months').format('MM/DD/YYYY');
                $('#date_start').daterangepicker({ startDate: now, endDate: today });

                function getSalesCenterAndCommodities(client_id,changeEvent=false) {
                    $.ajax({
                        url: "{{ route('ajax.getSalesCenterAndCommodity') }}",
                        type: "POST",
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'client_id': client_id
                        },
                        success: function(res) {
                            if (res.status === true) {
                                var html = '<option value="" selected>All</option>';
                                var commodities = res.data.commodity;
                                for (i = 0; i < commodities.length; i++) {
                                    html += '<option value="' + commodities[i].id + '">' + commodities[i].name + '</option>'
                                }
                                $('#commodity').html(html);

                                var shtml = '<option value="" selected>All</option>';
                                var sales_center = res.data.sales_centers;
                                for (i = 0; i < sales_center.length; i++) {
                                    shtml += '<option value="' + sales_center[i].id + '">' + sales_center[i].name + '</option>'
                                }
                                $('#sales_center').html(shtml);

                                @if(!empty($salesCenter))
                                if(changeEvent === false) {
                                    $('#sales_center').val(parseInt("{{$salesCenter}}"));
                                }                                
                                @endif

                                @if(!empty($commodity))
                                if(changeEvent === false) {
                                    $('#commodity').val(parseInt("{{$commodity}}"));
                                }
                                @endif
                                $('#sales_center,#commodity').select2().trigger('change');
                                $("#s2id_sales_center,#s2id_commodity").css('display', 'block');
                            } else {
                                console.log(res.message);
                            }
                        }
                    })
                }

                $("#client").change(function() {
                    var client_id = $("#client").val();
                    $(this).parsley().validate();
                    getSalesCenterAndCommodities(client_id,true);
                });

                if($("#client").val() > 0) {
                    getSalesCenterAndCommodities($("#client").val());
                }
                $("#export").click(function() {
                    $('#export-form').html('');
                    $clone = $("#filter-form").clone(true,true);
                    $('#export-form').append($clone.html()).submit();
                    
                });
            })
        </script>

        <!--table--show-->

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        @if($results && count($results) > 0)
                        <div class="client-bg-white mt30">
                            <div class="row ">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="cont_bx3 report-main-tab report-tabs-result">

                                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                                           
                                            <form id="export-form" action="{{route('reports.exports.daily')}}" style="display: none"> </form>
                                            <button class="btn btn-green pull-right mb15" id="export" >Export

                                           </button>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                                            <div class="table-responsive">
                                                <table class="table script-table enroll-table" id="enrolment_reports">
                                                    <thead>
                                                        <tr class="heading">
                                                            @foreach($results[0] as $heading => $value)

                                                            <td> {{ $heading }} </td>

                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>




                                                        <?php $i = 0; ?>
                                                        @foreach($results as $report)

                                                        <?php $i++;
                                                        if ($i % 2 == 0) {
                                                            $first_last_td_class = "";
                                                            $second_and_middle_td_class = "";
                                                        } else {
                                                            $first_last_td_class = "";
                                                            $second_and_middle_td_class = "";
                                                        }
                                                        ?>

                                                        @if($report->UtilityTypeName == 'Dual Fuel')

                                                        <?php

                                                        $reportdata = (new App\models\Reports)->sparkDualDataElectricCommodity();
                                                        if (count($reportdata) > 0) {
                                                            $report = $reportdata[0];
                                                        }
                                                        ?>
                                                        <tr>
                                                            @foreach($report as $headinglabel => $valueoflead )

                                                            <td class="{{$first_last_td_class}} electric {{$headinglabel}}">{{ $valueoflead }}</td>

                                                            @endforeach
                                                        </tr>
                                                        <?php
                                                        $reportdata = (new App\models\Reports)->sparkDualDataGasCommodity($report->ExternalSalesID);
                                                        if (count($reportdata) > 0) {
                                                            $report = $reportdata[0];
                                                        }
                                                        ?>
                                                        <tr>
                                                            @foreach($report as $headinglabel => $valueoflead )

                                                            <td class="{{$first_last_td_class}} gas {{$headinglabel}}">{{ $valueoflead }}</td>

                                                            @endforeach
                                                        </tr>
                                                        @else
                                                        <tr>
                                                            @foreach($report as $headinglabel => $valueoflead )

                                                            <td class="{{$first_last_td_class}}">{{ $valueoflead }}</td>

                                                            @endforeach
                                                        </tr>
                                                        @endif




                                                        @endforeach




                                                    </tbody>
                                                </table>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ $results->appends(request()->all())->links() }}
                        @elseif(!$results)
                        <span></span>
                        @else
                        <div class="client-bg-white mt30">
                            <div class="row" style="text-align: center;">
                                <h2>No Record Found</h2>
                            </div>
                        </div>
                        @endif
                        <!--end--bg-white-area-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
