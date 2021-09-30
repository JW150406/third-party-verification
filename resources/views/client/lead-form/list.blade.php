<div class="mt30">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">

                @if(auth()->user()->hasPermissionTo('add-new-form') && $client->isActive())
                    <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                        <a href="{{ route('client.create-contact-page', ['id' => $client->id]) }}"
                           class="btn btn-green pull-right" data-toggle="modal">Add New Form</a>
                    </div>
                @endif
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="form-table">
                        <thead>
                        <tr class="heading acjin">
                            <th>Sr. No.</th>
                            <th class="name160">Name</th>
                            <th>Date of creation</th>
                            <th>Last Updated</th>
                            <th class="lead-view-clr">Scripts</th>
                            <th class="action-width" style="width: 128px !important;">Action</th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<!--clone-lead--popup---start--->
<div class="modal fade confirmation-model" id="clone_lead_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                Form - <strong class="status-change-teamuser" id="ref_id_digit_to_clone"></strong> will be cloned.
            </div>

            <div class="modal-footer">
                <div class="btnintable bottom_btns pd0">
                    <div class="btn-group">
                        <a id="lead_clone_confirm" href="" class="btn btn-green">Confirm</a>
                        <a type="button" class="btn btn-red" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!--clone-lead--popup---end--->
<div class="team-addnewmodal client-new-tabs">
    <div class="modal fade" id="preview_lead_form">
    </div>
</div>
@include('client.lead-form.delete')

@push('scripts')    
    <script>
        $(document).ready(function () {
            $('#form-table').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                autoWidth: false,
                lengthChange: true,
                searching: false,
                hideEmptyCols: ['extn', 5],
                ajax: {
                    url: "{{ url('admin/client') }}" + "/{{ $client->id }}" + "/forms",
                },
                aaSorting: [
                    [1, 'asc']
                ],
                columns: [{
                    data: null
                },
                    {
                        data: 'formname',
                        name: 'formname'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'script',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    "searchable": false,
                    "orderable": false,
                    "width": "5%",
                    "targets": 0
                }],
                'fnDrawCallback': function () {
                    var table = $('#form-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#form-table_info')[0].style.display = 'block';
                        $('#form-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#form-table_info')[0].style.display = 'none';
                        $('#form-table_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#form-table_length')[0].style.display = 'none';
                    } else {
                        $('#form-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#form-table').DataTable();
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
        $('body').on('click', '.clone_lead_form', function(e) {
            var url = $_this(this).data('url');
            var formname = $_this(this).data('formname');
            $("#clone_lead_modal").modal('show');
            if (url == "" && formname == "") {
                e.preventDefault();
                return false;
            } else {
                $('#lead_clone_confirm').attr('href', url);
                $('#ref_id_digit_to_clone').html(formname);
            }
        });

        $('body').on('click', '.view_lead_form', function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('lead_forms.show') }}",
                data: {form_id: id},
                success: function(res) {
                    $('#preview_lead_form').html(res).modal('show');
                },
                error: function(res) {
                    alert('Woops, Something went wrong, please try again');
                }
            })
            
        });
    </script>

@endpush
