<div class="users-outer-tab mt30">
    <div class="row">
        <div class="col-12 top_sales">
            @if(auth()->user()->hasPermissionTo('add-client-user') && $client->isActive())
            <a href="#" class="btn btn-green pull-right mr15 client-user-modal" data-toggle="modal"  data-type="new" data-original-title="Add Client User" >Add Client User</a>
            @endif
        </div>
        <div class="sor_fil utility-btn-group mr15">
            <div class="search">
                <div class="search-container">
                    <button type="button">{!! getimage('images/search.png') !!}</button>
                    <input placeholder="Search" class="search_text" id="user_search" 
                           type="text" value="">

                </div>
            </div>
        </div>
    </div>
    <div class="sales_tablebx mt30">
        <div class="table-responsive">
            <table class="table" id="client-user-table">
                <thead>
                    <tr class="acjin">
                        <th>Sr.No.</th>
                        <th></th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Title</th>
                        <th>Role</th>
                        <th class="action-width">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@include('client.users_new.create')
@include('client.users_new.change_status')
@push('scripts')
<script>
    $(document).ready(function() {
    var userTable=    $('#client-user-table').DataTable( {
            processing: true,
            serverSide: true,
            lengthChange: true,
            //searchDelay: 2000,
            dom: 'tr<"bottom"lip>',
            ajax: { url:"{{ route('client.usersNew',$client_id) }}"},
            aaSorting: [[7, 'desc']],
            columns: [
                {data: null},
                {data: 'profile_picture',orderable:false,searchable:false},
                {data: 'full_name', name: 'first_name'},
                {data: 'email', name: 'email'},
                {data: 'title', name: 'title',defaultContent:'N/A'},
                {data: 'role', name: 'roles.display_name'},
                {data: 'action',orderable:false,searchable:false},
                {data: 'id',searchable:false,visible: false},
                {data: 'last_name',name:'last_name',visible: false,orderable: false},
            ],
            columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "width": "5%",
                "targets": 0
            }],
            'fnDrawCallback': function(){
                var table = $('#client-user-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#client-user-table_info')[0].style.display = 'block';
                    $('#client-user-table_paginate')[0].style.display = 'block';
                } else {
                    $('#client-user-table_info')[0].style.display = 'none';
                    $('#client-user-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#client-user-table_length')[0].style.display = 'none';
                } else {
                    $('#client-user-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#client-user-table').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        });

        $('#user_search').change(function () {
            var val = $(this).val();
            var regExSearch = val ? val.replace(" ", "%") : '';

            $('#client-user-table').DataTable().search(val).draw();
           
        })
        // this is for ajax datatable clicking on pagination button
        $('body').on('click','.dataTables_paginate .paginate_button',function(){     
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });
        
    }).on( 'processing.dt', function ( e, settings, processing ) {
        $(".tooltip").tooltip("hide");
    });
</script>

@endpush
