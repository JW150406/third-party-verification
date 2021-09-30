@extends('layouts.admin')
@section('content')

<?php
// $breadcrum = array(
//     array('link' => route('client.index'), 'text' =>  'Clients'),
//     array('link' => route("client.show", array($client->id)), 'text' =>  $client->name),
//     array('link' => route("client.show", array($client->id))."#Utilities", 'text' =>  "Utilities"),
//     array('link' => "", 'text' =>  'Bulk Upload'),
// );

    if(\Auth::user()->can(['all-clients'])) {
        $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');
    }
    $breadcrum[] = array('link' => route("client.show", array($client->id)), 'text' =>  $client->name);
    $breadcrum[] = array('link' => route("client.show", array($client->id))."#SalesCenter", 'text' =>  "Sales Centers");
    $breadcrum[] = array('link' => "", 'text' =>  $salescenter->name);

breadcrum($breadcrum);
?>
<div class="tpv-contbx">
    <div class="container ui-droppable" style="width: 1723px;">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                        <div class="client-bg-white">
                            <div class="message"></div>
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <h1>Bulk Upload</h1>
                                </div>

                                <div class="col-md-6">
                                    <a href="{{ route('client.salesagents.downloadSample', array(0)) }}" class="btn btn-green pull-right">Download Sample File</a>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="sales_tablebx mb30 mt30">
                                <p>File Description :</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="acjin">
                                                <th>Column name</th>
                                                <th>Description</th>
                                                <th>Data type (length)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="list-users">
                                                <td class="dark_c">Location</td>
                                                <td class="grey_c">You can enter the location name in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">First Name</td>
                                                <td class="grey_c">You can enter the first name in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Last Name</td>
                                                <td class="grey_c">You can enter the last name in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>                                           
                                            <tr class="list-users">
                                                <td class="dark_c">Email</td>
                                                <td class="grey_c">You can enter email in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Password</td>
                                                <td class="grey_c">You can enter password in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                             <tr class="list-users">
                                                <td class="dark_c">Agent Type</td>
                                                <td class="grey_c">You can enter the agent type in this field. </a></td>
                                                <td class="grey_c">tele/d2d</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Certified</td>
                                                <td class="grey_c">You can enter whether agent is certified or not in this field. </a></td>
                                                <td class="grey_c">1/0</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Certification Date</td>
                                                <td class="grey_c">You can enter date of certification in this field. </a></td>
                                                <td class="grey_c">Date in yyyy-mm-dd format</td>
                                            </tr>
                                             <tr class="list-users">
                                                <td class="dark_c">Certification Exp Date</td>
                                                <td class="grey_c">You can enter date of certification expiry in this field. </a></td>
                                                <td class="grey_c">Date in yyyy-mm-dd format</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">State Test</td>
                                                <td class="grey_c">You can enter whether state test yes or not in this field. </a></td>
                                                <td class="grey_c">1/0</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">State</td>
                                                <td class="grey_c">You can enter state names comma seperated in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Background Check</td>
                                                <td class="grey_c">You can enter whether background check is done or not in this field. </a></td>
                                                <td class="grey_c">1/0</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Drug Check</td>
                                                <td class="grey_c">You can enter whether drug check is done or not in this field. </a></td>
                                                <td class="grey_c">1/0</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">External Id</td>
                                                <td class="grey_c">You can enter external id in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Phone Number</td>
                                                <td class="grey_c">You can enter phone number in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Restrict State</td>
                                                <td class="grey_c">You can enter restrict state in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if ($messages = Session::get('dataErrors'))                        
                            <div class="alert alert-warning">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="scrollbar-inner bk-ht">
                                    <?php    array_walk($messages,'printDataErrors'); ?>
                                </div>
                            </div>
                        @endif
                        <div id="data-errors"></div>
                        <div class="client-bg-white mt30">

                            <form id="import-form" action="{{ route('client.salesagents.importAgents',array($client->id,$salescenter_id)) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="bulk-bottom-area">
                                    <!---new file-uploader------>
                                    <div class="col-md-3 col-md-offset-4">
                                        <div class="form-group mt15 mb30 text-left">
                                            <label class="text-left">Upload File</label>
                                            <div class="dropzone files-container " id="upload-file">
                                                <div class="fallback">
                                                    <input name="file" type="file"/>
                                                </div>
                                            </div>                                        
                                            @include('preview-dropzone')
                                        </div>
                                        <input type="hidden" name="upload_file">
                                    </div> 
                                    <!--end--new-file-uploader--->
                                    <div class="d-inline-block">
                                    <!--  <div class="form-group">
                                    <label class="d-block text-left" for="name">Upload Status</label>
                                    <input id="name" type="text" class="form-control required" name="name" value="" required="" autofocus="">
                                    </div>
                                    -->
                                    </div>
                                    <div class="row mt30">
                                        <div class="col-xs-12 col-md-12">
                                            <button class="btn btn-green mr15"  id="upload-btn" type="button">Upload</button>
                                            <a href="{{ route('client.salescenter.show', array($client->id,$salescenter->id))}}#SalesAgent"><button class="btn btn-red" type="button">Cancel</button></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/bulk-upload.js') }}"></script>
@endpush