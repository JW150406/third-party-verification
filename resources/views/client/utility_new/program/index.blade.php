<div class="row">
    <div class="col-md-12">
        <div class="cont_bx3 mt30 sor_fil">
            <div class="btn-group pull-right">
                @if(auth()->user()->hasPermissionTo('add-program') && $client->isActive())
                    <a href="#" class="btn btn-green mr15" data-toggle="modal" data-type="new" data-target="#add_program"
                       id="add-program-btn">Add Program</a>
                @endif
                @if((auth()->user()->hasPermissionTo('bulk-upload-program') || auth()->user()->hasPermissionTo('export-program')) && $client->isActive())
                    <button type="button" class="btn btn-green dropdown-toggle" data-toggle="dropdown"
                            aria-expanded="false">
                        More <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu employee-dropdown" role="menu">
                        @if(auth()->user()->hasPermissionTo('bulk-upload-program'))
                            <li><a href="{{route('utility.programs.bulkupload',$client_id)}}" type="button">Bulk
                                    Upload</a>
                            </li>
                        @endif
                        @if(auth()->user()->hasPermissionTo('export-program'))
                            <li><a href="{{route('utility.programs.exportProgram',$client_id)}}"
                                   type="button">Export</a>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
            <div class="btn-group pull-right btn-sales-all">
                <select name="filtter_active_inactive" id="active_inactive" class="select2 btn btn-green dropdown-toggle mr15 "
                        role="menu">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="sor_fil utility-btn-group mr15">
                <div class="search">
                    <div class="search-container">

                        <button type="button">{!! getimage('images/search.png') !!}</button>
                        <input placeholder="Search" id="program_search" type="text" value="">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="table-responsive mt30">
    <table id="program-table" class="table append-client-workflow-table">
        <thead>
        <tr>
            <th>Commodity</th>
            <th>Brand name</th>
            <th>Utility</th>
            <th>Customer Type</th>
            <th>Program name</th>
            <th>Program code</th>
            <th style="min-width: 7%;">Rate ($)</th>
            <th>Unit</th>
            <th style="min-width: 7%;">ETF ($)</th>
            <th style="min-width: 7%;">MSF ($)</th>
            <th style="min-width: 10%;">Term (months)</th>
            <th>{{ $customFields['custom_field_1'] ?? 'Custom Field 1' }}</th>
            <th>{{ $customFields['custom_field_2'] ?? 'Custom Field 2' }}</th>
            <th>{{ $customFields['custom_field_3'] ?? 'Custom Field 3' }}</th>
            <th>{{ $customFields['custom_field_4'] ?? 'Custom Field 4' }}</th>
            <th>{{ $customFields['custom_field_5'] ?? 'Custom Field 5' }}</th>
            <th style="min-width: 95px; text-align: center; padding: 0px ">Action</th>
        </tr>
        </thead>

    </table>
</div>
@php $customFieldsKey = array_keys($customFields);  @endphp
@include('client.utility_new.program.create')

@push('scripts')
    <script>
        var isVisibleField1 = isVisibleField2 = isVisibleField3 = isVisibleField4 = isVisibleField5 = false;

        @if(in_array('custom_field_1', $customFieldsKey))
            isVisibleField1 = true;
        @endif
        @if(in_array('custom_field_2', $customFieldsKey))
            isVisibleField2 = true;
        @endif
        @if(in_array('custom_field_3', $customFieldsKey))
            isVisibleField3 = true;
        @endif
        @if(in_array('custom_field_4', $customFieldsKey))
            isVisibleField4 = true;
        @endif
        @if(in_array('custom_field_5', $customFieldsKey))
            isVisibleField5 = true;
        @endif
        
        function getCustomerType() {
            $('#program_customer_type').html('');
            $.ajax({
                url: "{{route('client.getCustomerType',$client_id)}}",

                success: function (response) {
                    if (response.status == 'success') {
                        $('#program_customer_type').append("<option value=''>Select</option>");
                        $.each(response.data, function (key, value) {
                            $('#program_customer_type').append("<option value='" + value.id + "'>" + value.name + "</option>");
                        });
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }

        function getCommodityUnit(commodity_id=null, selected = '') {
            $('#program_unit').html('');
            $.ajax({
                url: "{{route('client.getCommodityUnit')}}",
                data: {commodity_id:commodity_id},
                success: function (response) {
                    if (response.status == 'success') {
                        $('#program_unit').append("<option value=''>Select</option>");
                        $.each(response.data, function (key, value) {
                            $('#program_unit').append("<option value='" + value.unit + "'>" + value.unit + "</option>");
                        });

                        if (selected != '') {
                            $("#program_unit").val(selected).trigger('change.select2');
                        }
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }
        $(document).ready(function () {
            var programTable = $('#program-table').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                autoWidth: false,
                //hideEmptyCols: ['extn', 11],
                lengthChange: true,
                ajax: {
                    url: "{{ route('utility.programs') }}",
                    data: function (d) {
                        d.client_id = "{{$client_id}}";
                        d.status = $('select#active_inactive option:selected').val();
                    }
                },
                aaSorting: [
                    [17, 'desc']
                ],
                columns: [
                    //{data: null},
                    {
                        data: 'commodity',
                        name: 'commodities.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'utilityname',
                        name: 'brand_contacts.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'provider',
                        name: 'utilities.fullname',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'customer_type_name',
                        name: 'customer_types.name',
                        defaultContent: ''
                    },
                    {
                        data: 'name',
                        name: 'name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },                    
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: 'unit_of_measure',
                        name: 'unit_of_measure'
                    },
                    {
                        data: 'etf',
                        name: 'etf'
                    },
                    {
                        data: 'msf',
                        name: 'msf'
                    },
                    {
                        data: 'term',
                        name: 'term',
                    },
                    {
                        data: 'custom_field_1',
                        name: 'custom_field_1',
                        visible: isVisibleField1
                    },
                    {
                        data: 'custom_field_2',
                        name: 'custom_field_2',
                        visible: isVisibleField2
                    },
                    {
                        data: 'custom_field_3',
                        name: 'custom_field_3',
                        visible: isVisibleField3
                    },
                    {
                        data: 'custom_field_4',
                        name: 'custom_field_4',
                        visible: isVisibleField4
                    },
                    {
                        data: 'custom_field_5',
                        name: 'custom_field_5',
                        visible: isVisibleField5
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
                'fnDrawCallback': function () {
                    var table = $('#program-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#program-table_info')[0].style.display = 'block';
                        $('#program-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#program-table_info')[0].style.display = 'none';
                        $('#program-table_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#program-table_length')[0].style.display = 'none';
                    } else {
                        $('#program-table_length')[0].style.display = 'block';
                    }
                }
            }).on( 'processing.dt', function ( e, settings, processing ) {
                $(".tooltip").tooltip("hide");
            });

            $("#active_inactive").change(function () {

                programTable.ajax.reload();
            });
            $('#program_search').change(function () {
                var val = $(this).val();
                var regExSearch = val ? val.replace(" ", "%") : '';
                programTable.search(regExSearch).draw();
            });

            // $('#add-program-btn').click(function (e) {                                     
            //     getCommodities();
            //     getCustomField();
            //     $(".ajax-error-message").html('');
            //     $(".help-block").remove('');
            //     $("#program-create-form")[0].reset();
            // });

            $('#programm_commodity').change(function (e) {
                var commodity_id = $(this).val();
                if ($(this).val() > 0) {
                    getCommodityUnit(commodity_id);
                    getBrandName(commodity_id);
                }
            });

            $('#programm_brandname').change(function (e) {
                var utilityname = $(this).val();
                var commodity_id = $('#programm_commodity').val();
                if (utilityname != '' && commodity_id != '') {
                    getUtilityProvider(commodity_id, utilityname);
                }
            });

            // $('#program_provider').change(function (e) {
            //     var fullname = $(this).val();
            //     if (fullname != '') {
            //         $("#program_market").html('');
            //         $.ajax({
            //             url: "{{route('ajax.getMarketByProvider')}}",
            //             data: {
            //                 fullname: fullname
            //             },
            //             success: function (response) {
            //                 if (response.status == 'success') {
            //                     $('#program_market').val(response.data[0].market);
            //                     // $.each( response.data, function( key, value ) {
            //                     //     $("#program_market").append('<option value="'+value.market+'" selected>'+value.market+'</option>');
            //                     // });
            //                 }
            //             },
            //             error: function (xhr) {
            //                 console.log(xhr);
            //             }
            //         });
            //     }
            // });

            $("#program-create-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (response) {
                        $('#add_program').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        programTable.ajax.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            printErrorMsgNew(form, xhr.responseJSON.errors);
                        }
                    }
                });
            });            

            $('#add_program').on('hidden.bs.modal', function () {
                $('#program-create-form').parsley().reset();
            });
            getCustomerType();
            
            // this is for ajax datatable clicking on pagination button
            $('body').on('click','.dataTables_paginate .paginate_button',function(){     
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
            });
        });
        // add key restriction for program rate
        // $('#program_rate').keypress(function(e){
        //     if (!$(this).val().match(/^\d{0,8}(\.\d{0,3})?$/)) {
        //         return false;
        //     }
        // });

        function getCommodities() {
            $('#programm_commodity').html('');
            $.ajax({
                url: "{{route('client.getCommodities',$client_id)}}",

                success: function (response) {
                    if (response.status == 'success') {
                        $('#programm_commodity').append("<option value=''>Select</option>");
                        $.each(response.data, function (key, value) {
                            $('#programm_commodity').append("<option value='" + value.id + "'>" + value.name + "</option>");
                        });
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }

        function getCustomField() {
            $('#custom-field-row').html('');
            $.ajax({
                url: "{{route('customFieldProgram.create')}}",
                data: {client_id: "{{$client_id}}"},
                success: function (response) {
                    if (response.status == 'success') {
                        $("#custom-field-row").html(response.view);
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }

        function getBrandName(commodity_id, selected='') {
            $("#programm_brandname").html('');
            $("#programm_brandname").append('<option value="">Select</option>');
            $.ajax({
                url: "{{route('ajax.getUtilityByCommodity')}}",
                data: {
                    commodity_id: commodity_id
                },
                success: function (response) {
                    if (response.status == 'success') {
                        $.each(response.data, function (key, value) {
                            $("#programm_brandname").append('<option value="' + value.name + '">' + value.name + '</option>');
                        });

                        if (selected != '') {
                            $("#programm_brandname").val(selected).trigger('change.select2');
                        }
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }

        function getUtilityProvider(commodity_id, utilityname, selected='') {
            $("#program_provider").html('');
            $("#program_provider").append('<option value="">Select</option>');
            $.ajax({
                url: "{{route('ajax.getProviderByUtilityName')}}",
                data: {
                    utilityname: utilityname,
                    commodity_id: commodity_id
                },
                success: function (response) {
                    if (response.status == 'success') {
                        $.each(response.data, function (key, value) {
                            $("#program_provider").append('<option value="' + value.fullname + '">' + value.fullnameMarket + '</option>');
                        });

                        if (selected != '') {
                            $("#program_provider").val(selected).trigger('change.select2');
                        }
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }

        $(document).on('click', '#add-program-btn', function(e) {
            getCommodities();
            getCustomField();
            
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            $("#all-unit").html('');
            $("#program-create-form")[0].reset();
            $(".select2").val('').trigger('change.select2');
            var action_type = $(this).data('type');
            
            var title = $(this).data('original-title');
            $('#add_program .modal-title').html(title);

            if (action_type == 'new') {
                $('#program-id').val('');
                $('#add_program').modal();
            } else {
                var id = $(this).data('id');
                $('#program-id').val(id);           
                
                $.ajax({
                    url: "{{route('program.edit')}}",
                    data: {
                        program_id: id
                    },
                    method:"post",
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#programm_commodity').val(response.data.commodity_id);
                            $('#programm_commodity').select2().trigger('change.select2');
                            getBrandName(response.data.commodity_id,response.data.utilityname);
                            getUtilityProvider(response.data.commodity_id,response.data.utilityname,response.data.provider)
                            $('#program_customer_type').val(response.data.customer_type_name_id);
                            $('#program_customer_type').select2().trigger('change.select2');
                            
                            getCommodityUnit(response.data.commodity_id, response.data.unit_of_measure);

                            $('#program_name').val(response.data.name);
                            $('#program_code').val(response.data.code);
                            $('#program_rate').val(response.data.rate);
                            $('#program_etf').val(response.data.etf);
                            $('#program_term').val(response.data.term);
                            $('#program_msf').val(response.data.msf);

                            $('input[name="custom_field_1"]').val(response.data.custom_field_1);
                            $('input[name="custom_field_2"]').val(response.data.custom_field_2);
                            $('input[name="custom_field_3"]').val(response.data.custom_field_3);
                            $('input[name="custom_field_4"]').val(response.data.custom_field_4);
                            $('input[name="custom_field_5"]').val(response.data.custom_field_5);
                            
                        } else {
                            console.log(response.message);    
                        }

                        $('#add_program').modal();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }
        });
    </script>


    @include('client.utility_new.program.delete')
@endpush
