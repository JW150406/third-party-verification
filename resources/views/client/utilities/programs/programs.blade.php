@extends('layouts.admin')

@section('content')
<?php
$breadcrum = array();
$breadcrum[] =  array('link' => '', 'text' =>  'Programs');
breadcrum($breadcrum);

?>
<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

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
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                        <div class="client-bg-white">
                            <h1>Select Options</h1>



                            <?php if (Auth::user()->can(['view-programs']) || Auth::user()->access_level == 'client') { ?>
                                <div class="program sales_tablebx mt30">

                                    <div id="utility-zipcode">
                                        <form class=" getclientutilities" enctype="multipart/form-data" role="form" method="GET" action="">
                                            {{ csrf_field() }}
                                            @if(Auth::user()->access_level =='tpv')
                                            <div class="col-sm-12 col-sm-4 col-md-4">
                                                <div class="zip-inputbx">
                                                    <label for="client">Select Client</label>
                                                    <select class="selectsearch select-box-admin" name="client" id="client">
                                                        <option value="">Select</option>
                                                        @if(count($clients)>0)
                                                        @foreach($clients as $client)
                                                        <option value="{{$client->id}}" <?php if ($client_id == $client->id) echo "selected='selected'"; ?>>{{$client->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    @if ($errors->has('client'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('client') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            @if( count($utilities) > 0)
                                            <div class="col-sm-12 col-sm-4 col-md-4">
                                                <div class="zip-inputbx">
                                                    <label for="utility">Select utility</label>
                                                    <select class="selectsearch select-box-admin" name="utility" id="utility">
                                                        <option value="">Select</option>
                                                        @if(count($utilities)>0)
                                                        @foreach($utilities as $utility_detail)
                                                        <option value="{{$utility_detail->id}}" <?php if ($uid == $utility_detail->id) echo "selected='selected'"; ?>>{{$utility_detail->utilityname}}
                                                            @if($utility_detail->commodity != '')
                                                            ({{$utility_detail->commodity}})
                                                            @endif
                                                            @if($utility_detail->market != '')
                                                            ({{$utility_detail->market}})
                                                            @endif
                                                        </option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    @if ($errors->has('utility'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('utility') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif


                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="program-submitbtn">
                                                    @if(Auth::user()->access_level =='tpv' || Auth::user()->access_level =='client')
                                                    <button class="btn btn-green" type="submit">Submit</button>
                                                    @endif
                                                    @if($client_id!="")
                                                    <!-- <a class="btn btn-green" href="{{ route('utility.programs.add',['client' => $client_id]) }}">Add New<span class="add"><img src="/images/add.png"></span></a> -->
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table mt30">
                                                        <thead>
                                                            <tr class="heading">
                                                                <th>Name</th>
                                                                <th>Code</th>
                                                                <th>State</th>
                                                                <th>Rate</th>
                                                                <th>ETF</th>
                                                                <th>MSF</th>
                                                                <th>Term</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $i = 0; ?>
                                                            @foreach ($programs as $key => $program)
                                                            <?php
                                                            $i++;
                                                            if ($i % 2 == 0) {
                                                                $first_last_td_class = "light_c";
                                                                $second_and_middle_td_class = "white_c";
                                                            } else {
                                                                $first_last_td_class = "dark_c";
                                                                $second_and_middle_td_class = "grey_c";
                                                            }
                                                            ?>
                                                            <tr class="list-users">
                                                                <td class="{{$first_last_td_class}}">{{ $program->name }}</td>
                                                                <td class="{{$second_and_middle_td_class}}">{{ $program->code }}</td>
                                                                <td class="{{$second_and_middle_td_class}}">{{ $program->state}}</td>
                                                                <td class="{{$second_and_middle_td_class}}">{{ $program->rate}}</td>
                                                                <td class="{{$second_and_middle_td_class}}">{{ $program->etf}}</td>
                                                                <td class="{{$second_and_middle_td_class}}">{{ $program->msf}}</td>
                                                                <td class="{{$first_last_td_class}}">{{ $program->term}}

                                                                    <?php if (Auth::user()->can(['delete-programs'])) { ?>
                                                                        <a class="btn program-delbtn" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Program" data-id="{{ $program->id }}" id="delete-program-{{ $program->id }}" data-programname="{{ $program->name }}" role="button"><img src="/images/cancel.png"></a>
                                                                    <?php } ?>
                                                                </td>

                                                            </tr>
                                                            @endforeach

                                                            @if(count($programs)==0)
                                                            <tr class="list-users">
                                                                <td colspan="7" class="text-center">No Record Found</td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>

                                                    @if(count($programs)>0)
                                                    <div class="btnintable bottom_btns">
                                                        {!! $programs->appends(['client' => $client_id])->links() !!}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade confirmation-model" id="Deleteprogram">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('utility.program.delete') }}" method="POST">
                <input type="hidden" value="" name="id" id="programid">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <!-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Program Action</h4>
                </div> -->

                <div class="modal-body text-center">
                    <div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
                    <div class="mt20">
                        Are you sure you want to delete <strong class="delete-program-name"></strong>?
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
