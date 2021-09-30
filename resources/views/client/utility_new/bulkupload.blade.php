@extends('layouts.admin')
@section('content')

<?php
if(Auth::user()->isAccessLevelToClient()) {
    $breadcrum = array(
        array('link' => route("client.show", array($client->id)), 'text' =>  $client->name),
        array('link' => route("client.show", array($client->id))."#Utilities", 'text' =>  "Utilities"),
        array('link' => "", 'text' =>  'Bulk Upload'),
    );
} else {
    $breadcrum = array(
        array('link' => route('client.index'), 'text' =>  'Clients'),
        array('link' => route("client.show", array($client->id)), 'text' =>  $client->name),
        array('link' => route("client.show", array($client->id))."#Utilities", 'text' =>  "Utilities"),
        array('link' => "", 'text' =>  'Bulk Upload'),
    );
}
 
breadcrum($breadcrum);
?>
<div class="tpv-contbx">
    <div class="container ui-droppable" style="width: 1723px;">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                        <div class="client-bg-white min-height-solve">
                            <div class="message"></div>
                            {{-- @if ($message = Session::get('success'))
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
                            @endif --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <h1>Bulk Upload</h1>
                                </div>

                                <div class="col-md-6">
                                    <a href="{{ route('client.utility.downloadSample') }}" class="btn btn-green pull-right">Download Sample File</a>
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
                                                <td class="dark_c">Commodity</td>
                                                <td class="grey_c">You can enter the commodity name in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Brand Name</td>
                                                <td class="grey_c">You can enter the brand name in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Utility Provider</td>
                                                <td class="grey_c">You can enter the utility provider in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Abbreviation</td>
                                                <td class="grey_c">You can enter the abbreviation in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Regex</td>
                                                <td class="grey_c">You can enter the regular expression in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Regex Message</td>
                                                <td class="grey_c">You can enter the regular expression message in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Zipcodes</td>
                                                <td class="grey_c">You can enter multiple zipcodes with comma seprated in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Account Number Type</td>
                                                <td class="grey_c">You can enter Account Number Type(placeholder) in this field. </a></td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if ($messages = Session::get('dataErrors'))                        
                            <div class="alert alert-warning" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="scrollbar-inner bk-ht">
                                    <p style="color: red; font-weight: bold;"> Bulk Upload Failed.</p>
                                    <?php    array_walk($messages,'printDataErrors'); ?>
                                </div>
                            </div>
                        @endif
                        <div id="data-errors"></div>
                        <div class="client-bg-white  min-height-solve mt30">

                            <form id="import-form" action="{{ route('client.utility.importNew',['client' =>$client->id]) }}" method="POST" enctype="multipart/form-data">
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
                                            <a href="{{ route('client.show', array($client->id))}}#Utilities"><button class="btn btn-red" type="button">Cancel</button></a>
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
