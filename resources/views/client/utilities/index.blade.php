@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' => '', 'text' =>  'Utilities');

breadcrum($breadcrum);
?>

<div class="tpv-contbx">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            @if(Auth::user()->access_level =='tpv' )
            <div class="cont_bx3 salescenter_contbx">
                <h1>Select Client</h1>
                <form enctype="multipart/form-data" role="form" method="GET" action="">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-4">
                            <p>Select Client</p>
                            <div class="dropdown {{ $errors->has('name') ? ' has-error' : '' }}">
                                <select class="selectmenu select-box-admin" name="client" id="client">
                                    <option value="">Select</option>
                                    @if(count($clients)>0)
                                    @foreach($clients as $client)
                                    <option value="{{$client->id}}" <?php if ($client_id == $client->id) echo "selected='selected'"; ?>>{{$client->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-4">
                            <button class="btn btn-green" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
        <?php if (Auth::user()->can(['view-utility', 'view-zipcodes']) || Auth::user()->access_level == 'client') { ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3 utility-outer">
                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs mt30">
                            <div class="client-bg-white">
                                @if($client_id!="")

                                <div class="sor_fil utility-btn-group">
                                    <div class="col-xs-12 col-sm-12 col-md-12 search">
                                        <div class="search-container">
                                            <form action="" name="" method="get">
                                                <input type="hidden" name="client" value="{{$client_id}}">
                                                <button type="submit"><img src="/images/search.png" /></button>
                                                <input placeholder="Search" name="search_text" type="text" value="{{$search_text}}">

                                            </form>

                                        </div>
                                        <div class=" top_sales">
                                            <?php if (Auth::user()->can(['create-update-utility']) || Auth::user()->access_level == 'client') { ?>
                                                <a href="{{ route('client.utility.addnew',['client' => $client_id]) }}" class="btn btn-green" data-toggle="modal" data-target="#addnew_utility">Add New Utility</a>
                                            <?php } ?>
                                            <?php if (Auth::user()->can(['create-programs', 'create-update-utility']) || Auth::user()->access_level == 'client') { ?>
                                                <a class="btn btn-green" href="{{ route('client.utility.import',['client' => $client_id]) }}">Import from CSV</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class=" sales_tablebx mt30">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr class="acjin">
                                                    <th>Commodity</th>
                                                    <th>Name</th>
                                                    <th>Market</th>
                                                    <th>Assigned Zipcode</th>
                                                    <th class="visi-hidden" style="width:200px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $j = 0; ?>
                                                @foreach ($utilities as $key => $utility)
                                                <?php
                                                    $j++;
                                                    if ($j % 2 == 0) {
                                                        $first_last_td_class = "light_c";
                                                        $second_and_middle_td_class = "white_c";
                                                    } else {
                                                        $first_last_td_class = "dark_c";
                                                        $second_and_middle_td_class = "grey_c";
                                                    }
                                                    ?>
                                                <tr>
                                                    <td class="{{$first_last_td_class }}">{{ $utility->commodity }}</td>
                                                    <td class="{{$second_and_middle_td_class }}">{{ $utility->utilityname }}</td>
                                                    <td class="{{$second_and_middle_td_class }}">{{ $utility->market}}</td>
                                                    <td class="{{$second_and_middle_td_class }}">{{ $utility->zip}}</td>
                                                    <td class="{{$first_last_td_class }}">
                                                        <div class="btn-group">
                                                            <!-- <a class="btn"
                                                        href="{{ route('client.utility.Compliances',['utility_id' => $utility->id,'client_id' =>  $client_id ]) }}"
                                                        data-toggle="tooltip"
                                                        data-placement="top" data-container="body"
                                                        title=""
                                                        data-original-title="View Compliance Templates"
                                                        role="button"><?php //echo getimage("images/view_g.png");
                                                                            ?></a> -->

                                                            <a class="btn" href="{{ route('client.utility.view',['id' => $utility->id]) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Utility" role="button"><?php echo getimage("images/view.png"); ?></a>

                                                            <?php if (Auth::user()->can(['create-update-utility']) || Auth::user()->access_level == 'client') { ?>
                                                                <a class="btn" href="{{ route('client.utility.edit',$utility->id) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Utility" role="button"><?php echo getimage("images/edit.png"); ?></a>
                                                            <?php } ?>

                                                            <?php if (Auth::user()->can(['create-update-utility']) || Auth::user()->access_level == 'client') { ?>
                                                                <a class="btn delete-utility" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete utility" data-id="{{ $utility->id }}" id="delete-utility-{{ $utility->id }}" data-utilityname="{{ $utility->utilityname }}" role="button"><?php echo getimage("images/cancel.png"); ?></a>

                                                            <?php } ?>
                                                        </div>


                                                    </td>
                                                </tr>
                                                @endforeach
                                                @if(count($utilities)==0)
                                                <tr class="list-users">
                                                    <td colspan="5" class="text-center">No Record Found</td>
                                                </tr>
                                                @endif

                                            </tbody>
                                        </table>

                                        <div class="btnintable bottom_btns">
                                            @if(count($utilities)>0)
                                            {!! $utilities->appends(['client' => $client_id,'search_text' => $search_text])->links() !!}

                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
    </div>
</div>


@include('client.utilities.utilitypoup')
<div class="team-addnewmodal">
    <div class="modal fade" id="addnew_utility" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
</div>
@endsection
