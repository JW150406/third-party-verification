@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
    array('link' => "", 'text' =>  'Clients')
);
breadcrum($breadcrum);
?>
<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="client-bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    <h1 class="mt10">All Clients</h1>
                                </div>
                                <div class="col-md-6">
                                    <?php if (Auth::user()->can(['add-client'])) { ?>
                                        <a href="{{ route('client.create') }}" class="btn btn-green pull-right">Add New Client</a>
                                    <?php } ?>
                                </div>
                            </div>
                            @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissable">
                                {{ session()->get('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @php session()->forget('success') @endphp
                            @endif
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    {{ $message }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @php session()->forget('error') @endphp
                            @endif
                            <div class="message"></div>
                        <?php if (Auth::user()->can(['all-clients'])) { ?>
                            <div class="sales_tablebx mt30">
                                <div class="table-responsive">
                                    <table class="table" id="client-table">
                                        <thead>
                                            <tr class="list-users">
                                                <th class="sr-width">Sr. No.</th>
                                                <th class="all-clogo">Logo</th>
                                                <th>Client</th>
                                                <th>Code</th>
                                                <th class="address-width">Address</th>
                                                <th>Contact Number</th>                                              
                                                <th class="action-width">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
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


@endsection
@push('scripts')
<?php if (Auth::user()->can(['all-clients'])) { ?>
<script>
    $(document).ready(function() {
        $('#client-table').DataTable( {
            dom: 'tr<"bottom"lip>',
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: true,
            searchDelay: 2000,
            pageLength: 10,
            ajax: "{{ route('client.index') }}",
            aaSorting: [[7, 'desc']],
            columns: [
                {data: null},
               // {data: 'id', name: 'id'},
                {data: 'icon', name: 'logo',orderable:false,searchable:false},
                {data: 'name', name: 'name'},
                {data: 'code', name: 'code'},
                {data: 'street', name: 'street'},
                {data: 'contact_info', name: 'contact_info'},
                {data: 'action',orderable:false,searchable:false},
                {data: 'created_at',searchable:false,visible: false},
            ],
            columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "width": "5%",
                "targets": 0
            }],
            'fnDrawCallback': function(){
                var table = $('#client-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#client-table_info')[0].style.display = 'block';
                    $('#client-table_paginate')[0].style.display = 'block';
                } else {
                    $('#client-table_info')[0].style.display = 'none';
                    $('#client-table_paginate')[0].style.display = 'none';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#client-table').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        }).on( 'processing.dt', function ( e, settings, processing ) {
            $(".tooltip").tooltip("hide");
        });

        // this is for ajax datatable clicking on pagination button
        $('body').on('click','.dataTables_paginate .paginate_button',function(){     
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });
    });
    
</script>
@include('client.client-confirmation')
<?php } ?>
@endpush
