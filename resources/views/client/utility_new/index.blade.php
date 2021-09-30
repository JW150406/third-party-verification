<div class="mt30">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3 sor_fil">
                <div class="btn-group pull-right mr15">

                    <?php if (Auth::user()->hasPermissionTo('add-utility-provider') && $client->isActive()) { ?>

                    <a href="#" class="btn btn-green mr15" data-toggle="modal" data-target="#addnew_utility"
                       id="addnew_utility-btn" data-type="add">Add Utility</a>

                    <?php } ?>
                    @if(auth()->user()->hasPermissionTo('bulk-upload-utility') && auth()->user()->hasPermissionTo('export-utility') && $client->isActive())
                        <button type="button" class="btn btn-green dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            More <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu employee-dropdown" role="menu">
                            @if(auth()->user()->hasPermissionTo('bulk-upload-utility'))
                                <li><a href="{{ route('client.utility.bulkupload',['client' => $client->id]) }}"
                                       type="button">Bulk Upload</a></li>
                            @endif
                            @if(auth()->user()->hasPermissionTo('export-utility'))
                                <li><a href="{{route('client.utility.exportUtility',['client_id' => $client->id])}}"
                                       type="button">Export</a></li>
                            @endif
                            <li><a href="{{ route('client.utility.bulkupload.validations',['client' => $client->id]) }}"
                                   type="button">Validations Bulk Upload</a></li>
                            <li><a href="{{ route('client.utility.bulkupload.mappings',['client' => $client->id]) }}"
                                   type="button">Mappings Bulk Upload</a></li>
                        </ul>
                    @endif
                </div>
                <div class="sor_fil utility-btn-group mr15">
                    <div class="search">
                        <div class="search-container">
                            <button type="button">{!! getimage('images/search.png') !!}</button>
                            <input placeholder="Search" class="search_text" id="utility_search" name="search_utility" type="text" value="">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="utility-table">
                        <thead>
                        <tr class="heading acjin">
                            <th>Sr. No.</th>
                            <th>Commodity</th>
                            <th>Brand Name</th>
                            <th>Utility</th>
                            <th>States</th>
                            <th class="zipcode-width">Zipcodes</th>
                            <th class="action-width">Validation</th>
                            <th class="action-width">Utility Mapping</th>
                            <th class="action-width">Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('client.utility_new.create')
@include('client.utility_new.view-validate')
@include('client.utility_new.view-mapping')

@push('scripts')
    <script>
        $(document).ready(function () {
            var utilityTable = $('#utility-table').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                autoWidth: false,
                lengthChange: true,
                ajax: {
                    url: "{{ route('utilities.index') }}",
                    data: {
                        client_id: "{{$client_id}}"
                    }
                },
                aaSorting: [
                    [8, 'desc']
                ],
                columns: [{
                    data: null
                },
                    {
                        data: 'utility_commodity.name',
                        name: 'utilityCommodity.name'
                    },
                    {
                        data: 'name',
                        name: 'brand_contacts.name'
                    },
                    {
                        data: 'fullname',
                        name: 'fullname'
                    },
                    {
                        data: 'states',
                        name: 'utilityZipcodes.zipCode.state',
                        searchable: true
                    },
                    {
                        data: 'zipcode',
                        name: 'utilityZipcodes.zipCode.zipcode',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'action_validation',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action_mapping',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        searchable: false,
                        visible: false
                    },
                ],
                columnDefs: [{
                    "searchable": false,
                    "orderable": false,
                    "width": "5%",
                    "targets": 0,
                }],
                'fnDrawCallback': function () {
                    var table = $('#utility-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#utility-table_info')[0].style.display = 'block';
                        $('#utility-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#utility-table_info')[0].style.display = 'none';
                        $('#utility-table_paginate')[0].style.display = 'none';
                    }

                    if (info.recordsTotal < 10) {
                        $('#utility-table_length')[0].style.display = 'none';
                    } else {
                        $('#utility-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#utility-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            }).on( 'processing.dt', function ( e, settings, processing ) {
                $(".tooltip").tooltip("hide");
            });
            
            $('#utility_search').change(function (e) {
                var val = $(this).val();
                var regExSearch = val ? val.replace(" ", "%") : '';
                $('#utility-table').DataTable().search(regExSearch).draw();
            })
            // this is for ajax datatable clicking on pagination button
            $('body').on('click','.dataTables_paginate .paginate_button',function(){     
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
            });
        });

        
    </script>

    @include('client.utility_new.delete')
@endpush