@extends('layouts.admin')

@section('content')
<?php
$breadcrum = array();
$breadcrum[] =  array('link' => '', 'text' =>  "Reports");
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
                                    <h1 data-toggle="collapse" href="#ShowFilter"> Enrollment Report</h1>
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
                                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    @endif
                                                    <form enctype="multipart/form-data" role="form" method="get" action="">
                                                        {{ csrf_field() }}

                                                        <div class="row">
                                                            <div class="col-sm-2 col-md-2">
                                                                <div class="form-group {{ $errors->has('date_start') ? ' has-error' : '' }}">
                                                                    <label for="date_start">Date Range<sup class="redtext">*</sup></label>
                                                                    <input id="date_start" autocomplete="off" required type="text" class="form-control daterange" name="date_start" value="{{ old('date_start') }}@if(isset($request['date_start'])){{$request['date_start']}}@endif" placeholder="Date Range">
                                                                    @if ($errors->has('date_start'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('date_start') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('client') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Client</label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="salesvendor" name="client">
                                                                            <option value="">All Clients</option>

                                                                            @if( count($clients) > 0)
                                                                            @foreach($clients as $client)
                                                                            <option value="{{$client->id}}" @if(isset($request['client']) && $request['client']==$client->id ) selected @endif >{{$client->name}}</option>
                                                                            @endforeach
                                                                            @endif
                                                                        </select>
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
                                                                <div class="form-group {{ $errors->has('client') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Sales Center</label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="salesvendor" name="client">
                                                                            <option value="">Sales Centers 1</option>
                                                                            <option value="">Sales Centers 2</option>
                                                                            <option value="">Sales Centers 3</option>
                                                                            <option value="">Sales Centers 4</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end-col-3-->

                                                            <div class="col-sm-2 col-md-2">
                                                                <div class="form-group {{ $errors->has('client') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Commodity</label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="salesvendor" name="client">
                                                                            <option value="">Commodity 1</option>
                                                                            <option value="">Commodity 2</option>
                                                                            <option value="">Commodity 3</option>
                                                                            <option value="">Commodity 4</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end-col-3-->

                                                            <div class="col-sm-2 col-md-2">
                                                                <div class="form-group {{ $errors->has('client') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Verification Status</label>
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 no-search selectclientlocations_report" id="salesvendor" name="client">
                                                                            <option value="">Yes</option>
                                                                            <option value="">No</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end-col-3-->

                                                            <div class="col-sm-12 col-md-12">
                                                                <div class="btnintable bottom_btns mb15 mt15 pd0 pull-right">
                                                                    <div class="btn-group">
                                                                        <button class="btn btn-green" type="submit">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <!-- <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('referenceid') ? ' has-error' : '' }}">
                                                                    <label for="referenceid">Reference ID#</label>
                                                                    <input id="referenceid" autocomplete="off" type="text" class="form-control" name="referenceid" value="{{ old('referenceid') }}@if(isset($request['referenceid'])){{ $request['referenceid'] }}@endif" placeholder="Reference ID#">

                                                                    @if ($errors->has('referenceid'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('referenceid') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('salesagentid') ? ' has-error' : '' }}">
                                                                    <label for="salesagentid">Sales Center Agent ID</label>
                                                                    <input id="salesagentid" autocomplete="off" type="text" class="form-control" name="salesagentid" value="{{ old('salesagentid') }}@if(isset($request['salesagentid'])){{ $request['salesagentid'] }}@endif" placeholder="Sales Center Agent ID">

                                                                    @if ($errors->has('salesagentid'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('salesagentid') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                          
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('tpvagentid') ? ' has-error' : '' }}">
                                                                    <label for="tpvagentid">TPV Agent ID</label>
                                                                    <input id="tpvagentid" autocomplete="off" type="text" class="form-control" name="tpvagentid" value="{{ old('tpvagentid') }}@if(isset($request['tpvagentid'])){{ $request['tpvagentid'] }}@endif" placeholder="TPV Agent ID">

                                                                    @if ($errors->has('tpvagentid'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('tpvagentid') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('accountnumber') ? ' has-error' : '' }}">
                                                                    <label for="accountnumber">Account Number</label>
                                                                    <input id="accountnumber" autocomplete="off" type="text" class="form-control" name="accountnumber" value="{{ old('accountnumber') }}@if(isset($request['accountnumber'])){{ $request['accountnumber'] }}@endif" placeholder="Account Number">

                                                                    @if ($errors->has('accountnumber'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('accountnumber') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                           
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('vendorstatus') ? ' has-error' : '' }}">
                                                                    <label for="vendorstatus">Vendor status</label>
                                                                    <select class="select2 no-search vendorstatus" id="vendorstatus" name="vendorstatus" data-width="100%" data-minimum-results-for-search="Infinity">
                                                                        <option value="">All Status</option>
                                                                        <option value="active" @if(isset($request['vendorstatus']) && $request['vendorstatus']=='active' ) selected @endif>Active</option>
                                                                        <option value="inactive" @if(isset($request['vendorstatus']) && $request['vendorstatus']=='inactive' ) selected @endif>In-active</option>
                                                                    </select>
                                                                    @if ($errors->has('vendorstatus'))
                                                                    <span class="help-block text-danger">
                                                                        <strong>{{ $errors->first('vendorstatus') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                          

                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group {{ $errors->has('program') ? ' has-error' : '' }}">
                                                                    <label for="salesvendor">Program</label>
                                                                    <select class="select2 no-search vendor_programs" id="vendor_programs" name="program">
                                                                        <option value="">All Programs</option>
                                                                        @if( count($programs) > 0)
                                                                        @foreach($programs as $program)
                                                                        <option value="{{$program->id}}" @if(isset($request['program']) && $request['program']==$program->id ) selected @endif >{{$program->program_name}} ({{$program->code}}) {{$program->client_name}}</option>
                                                                        @endforeach
                                                                        @endif
                                                                    </select>
                                                                    @if ($errors->has('program'))
                                                                    <span class="help-block text-danger">
                                                                        <strong>{{ $errors->first('program') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group  {{ $errors->has('locationid') ? ' has-error' : '' }}">
                                                                    <label for="location_select">Location</label>
                                                                    <div class="updatelocaton_according_to_client">
                                                                        <select name="locationid" class="select2 no-search location_select" id="location_select">
                                                                            <option value="">All Locations</option>
                                                                            @foreach($locations as $location)
                                                                            <option value="{{$location->id}}" @if(isset($request['locationid']) && $request['locationid']==$location->id ) selected @endif >{{$location->name}}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        @if ($errors->has('locationid'))
                                                                        <span class="help-block">
                                                                            <strong>{{ $errors->first('locationid') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">

                                                                <div class="form-group {{ $errors->has('userstatus') ? ' has-error' : '' }}">
                                                                    <label for="userstatus">Sales agent status</label>
                                                                    <div class="dropdown select-dropdown">
                                                                        <select class="select2 no-search userstatus" id="userstatus" name="userstatus">
                                                                            <option value="">All Status</option>
                                                                            <option value="active" @if(isset($request['userstatus']) && $request['userstatus']=='active' ) selected @endif>Active</option>
                                                                            <option value="inactive" @if(isset($request['userstatus']) && $request['userstatus']=='inactive' ) selected @endif>In-active</option>

                                                                        </select>
                                                                        @if ($errors->has('userstatus'))
                                                                        <span class="help-block text-danger">
                                                                            <strong>{{ $errors->first('userstatus') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">

                                                                <div class="form-group  {{ $errors->has('salesagent') ? ' has-error' : '' }}">
                                                                    <label for="salesbyagent">Agent Sales Activity </label>
                                                                    <div class="dropdown select-dropdown">
                                                                        <div class="updatsalesagents">
                                                                            <select name="salesagent" class="select2 no-search salesagentfilter" id="salesbyagent">
                                                                                <option value="">All sales agents</option>
                                                                                @foreach($salesagents as $salesagent)
                                                                                <option value="{{$salesagent->id}}" @if(isset($request['salesagent']) && $request['salesagent']==$salesagent->id ) selected @endif >{{$salesagent->first_name}} {{$salesagent->last_name}} ({{$salesagent->userid}}) </option>
                                                                                @endforeach
                                                                            </select>

                                                                            @if ($errors->has('salesagent'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('salesagent') }}</strong>
                                                                            </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">

                                                                <div class="form-group  {{ $errors->has('status') ? ' has-error' : '' }}">
                                                                    <label for="selectsaletype">Sale type</label>
                                                                    <div class="dropdown select-dropdown">
                                                                        <select name="status" class="select2 no-search" id="selectsaletype">
                                                                            <option value="">Both</option>
                                                                            <option value="verified" @if(isset($request['status']) && $request['status']=='verified' ) selected @endif>Good Sale</option>
                                                                            <option value="decline" @if(isset($request['status']) && $request['status']=='decline' ) selected @endif>Bad Sale</option>
                                                                        </select>

                                                                        @if ($errors->has('status'))
                                                                        <span class="help-block">
                                                                            <strong>{{ $errors->first('status') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">

                                                                <div class="form-group text-left radio-btns flex">
                                                                    <label class="radio-inline ml10">
                                                                        <input type="radio" id="datareport" name="tpvreport" value="1" @if( ( isset($request['tpvreport']) && $request['tpvreport']=='1' ) || !isset($request['tpvreport']) ) checked @endif>Data Report
                                                                    </label>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group text-left radio-btns flex">
                                                                    <label class="radio-inline ml10">
                                                                        <input type="radio" id="enrollment_report" name="tpvreport" value="2" @if(isset($request['tpvreport']) && $request['tpvreport']=='2' ) checked @endif>Enrollment Report
                                                                    </label>
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="form-group text-left radio-btns flex">
                                                                    <label class="radio-inline ml10">
                                                                        <input type="radio" id="salesagentactivity" name="tpvreport" value="3" @if(isset($request['tpvreport']) && $request['tpvreport']=='3' ) checked @endif>Sales agent activity
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-sm-3 col-md-3">
                                                                <div class="btnintable bottom_btns mt15 pd0">
                                                                    <div class="btn-group">
                                                                        <button class="btn btn-green" type="submit">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </div>-->
                                                        </div> 
                                                        <!--end--row-->
                                                        <!-- <small class="redtext">All fields mentioned with a (*) are required</small> -->
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

        <!--table--show-->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="client-bg-white mt30">

                            <div class="row ">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="cont_bx3 report-main-tab report-tabs-result">
                                        @if(isset($request['tpvreport']))
                                        @if(count($results) > 0)
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <h1>Report Details</h1>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <a class="btn btn-green pull-right mb15" href="{{ route('reports.exports',$export_params) }}">Export <span class="add"><i class="fa fa-download"></i></span> </a>
                                            </div>
                                        </div>
                                        @endif
                                        @endif
                                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                                            @if(isset($request['tpvreport']) && $request['tpvreport'] == 1)

                                            @include('reports.reportdata')


                                            @endif
                                            @if(isset($request['tpvreport']) && $request['tpvreport'] == 2)

                                            @include('reports.sparkreportdata')


                                            @endif

                                            @if(isset($request['tpvreport']) && $request['tpvreport'] == 3)

                                            @include('reports.salesactivity.reportdata')


                                            @endif
                                            @if(isset($request['tpvreport']))
                                            @if(count($results)> 0)
                                            <div class="btnintable bottom_btns">
                                                {!! $results->appends($query_params)->links()!!}
                                            </div>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cont_bx3 report-compliance-tab report-tabs-result">

                                <div id="showutilities_templates" style="display:none">
                                    <div class="show_loading"></div>
                                    <form action="{{route('report.batchexportall')}}" class="exportall_batch_templates" method="get">
                                        <input type="hidden" name="daterange" value="" id="export_all_date_range">
                                        <div class="col-xs-12 col-sm-12 col-md-12" style="padding-top:20px;">
                                            <div class="all-export-zip" style="display:none;">
                                                <button class="btn btn-green export_all" type="submit"> Export all <span class="add" style="background:transparent"><i class="fa fa-download"></i></span> </button>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                                            <div class="table-responsive dynamic-table">
                                                <table class="table template_lists">
                                                    <thead>
                                                        <tr class="heading">
                                                            <th style="width:30px"> <input type='checkbox' class="select_all_items" name='' value=''> Select all</th>
                                                            <th>Template Name</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="new_elements_for_templates_report">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <!--end--bg-white-area-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection