@extends('layouts.admin')

@section('content')
<ol class="breadcrumb bc-3">
    <li>
        <a href="{{route('dashboard')}}"><i class="fa fa-home"></i>Home</a>
    </li>

    @if( Auth::user()->access_level =='tpv')
    <li>
        <a href="{{route('client.index')}}">Clients</a>
    </li>
    <li>
        <a href="{{ route('client.show',$client->id) }}">{{$client->name}}</a>
    </li>

    @endif


    <li class="active">
        <strong>Compliance Reports</strong>
    </li>
</ol>


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    Report Filters
                </div>
            </div>
            <div class="panel-body">
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
                <form class="form-horizontal" enctype="multipart/form-data" role="form" method="get" action="">
                    {{ csrf_field() }}
                    <input type="hidden" name="search" value="1">
                    <?php
                    $request = Request::all();


                    ?>
                    <script type="text/javascript">
                        window.exportall_compliance = "{{route('client.compliance-reports-export-all',$client->id)}}";
                    </script>

                    <div class="form-group">
                        <label for="daterange" class="col-md-2 control-label inline-block text-right">Select Template</label>

                        <div class="col-md-4 inline-block ">
                            <select class="selectmenu form-control" name="template">
                                <option value="">Select</option>
                                @if(count($compliance_templates) > 0)
                                @foreach($compliance_templates as $compliancetemplate)
                                <option value="{{ $compliancetemplate->id}}" @if(isset($request['template']) && $request['template']==$compliancetemplate->id ) selected @endif>{{ $compliancetemplate->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            @if ($errors->has('template'))
                            <span class="help-block text-danger">
                                <strong>{{ $errors->first('template') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="daterange" class="col-md-2 control-label inline-block text-right">Date Range</label>

                        <div class="col-md-4 inline-block ">
                            <input id="date_start" autocomplete="off" type="text" class="form-control daterange" name="date_start" value="{{ old('date_start') }} @if(isset($request['date_start']) ) {{$request['date_start']}} @endif">


                            @if ($errors->has('date_start'))
                            <span class="help-block">
                                <strong>{{ $errors->first('date_start') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                            <a class="btn btn-success btn-icon icon-left export-all-templates" href="javascript:void(0)" style="padding: 13px 39px;"> <i class="fa fa-download" style="padding-top:14px;"></i> Export All</a>
                        </div>
                    </div>
                </form>

                @if(isset($request['search']) )
                <div class="table-responsive">
                    @include('client.compliance-reports.results')
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection