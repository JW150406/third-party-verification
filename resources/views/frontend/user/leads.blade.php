@extends('layouts.app')
@section('content')

<style>
    .space-none {
        margin-top: 15px;
    }

    .cont_bx3 .pdlr0 {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>
<section class="select-filter hide ">
    <div class="filter-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <a href="#" id="filter-back"><img src="images/left-aerrow.png" />Back</a>
                    <div class="select-filter-wrapper">
                        <a href="#" class="reset-link">Reset Filter</a>
                        <label>
                            Filter <span class="filter-selected"></span>
                            <span class="filter-icon"><a href="#"><img src="images/filter-icon.png"></a></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="select-filter-list scrollbar-dynamic">
        <div class="container">
            <form role="form" id="filter-form" method="get" action="{{ route('profile.leads') }}">
                <div class="row">
                    <div class="col-sm-4 col-md-4">
                        <div class="search-box">
                            <h5>Date/Range</h5>
                            <div class="form-group search-wrapper">
                                <img class="fa-search icon-date" src="images/calender.png"></img>
                                <input id="date_start" type="text" class="form-control daterange" name="date_range" value="{{ !empty(Request::get('date_range')) ? Request::get('date_range') : '' }}" placeholder="Select...">
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="search-box">
                            <h5>Lead Type</h5>
                            <div class="form-group search-wrapper">
                                <!-- <img class="fa-search" src="images/search.png"></img>
                                <input type="text" name="q" class="form-control" placeholder="Search..."> -->
                            </div>
                            <div class="search-list ">
                                <ul class="ht142">
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="pending" {{ Request::get('status') === "pending" ? 'checked' : '' }}>Pending
                                        </label>
                                    </li>
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="verified" {{ Request::get('status') === "verified" ? 'checked' : '' }}>Verified
                                        </label>
                                    </li>
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="decline" {{ Request::get('status') === "decline" ? 'checked' : '' }}>Declined
                                        </label>
                                    </li>
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="hangup" {{ Request::get('status') === "hangup" ? 'checked' : '' }}>Disconnected
                                        </label>
                                    </li>
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="cancel" {{ Request::get('status') === "cancel" ? 'checked' : '' }}>Cancelled
                                        </label>
                                    </li>
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="expired" {{ Request::get('status') === "expired" ? 'checked' : '' }}>Expired
                                        </label>
                                    </li>
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="status" value="self-verified" {{ Request::get('status') === "self-verified" ? 'checked' : '' }}>Self Verified
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="search-box">
                            <h5>State</h5>
                            <div class="form-group search-wrapper">
                                <img class="fa-search" src="images/search.png"></img>
                                <input type="text" class="form-control" placeholder="Search..." id="myInput" onkeyup="myFunction()">
                            </div>
                            <div class="search-list">
                                <ul id="myUL" class="scrollbar-inner">
                                    @forelse($states as $state)
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            <input type="radio" name="state" value="{{ $state->state }}" {{ Request::get('state') == $state->state ? 'checked' : '' }}> {{ $state->state }}
                                        </label>
                                    </li>
                                    @empty
                                    <li class="radio-btns">
                                        <label class="radio-inline">
                                            Empty State
                                        </label>
                                    </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>



                </div>
                <div class="row filter-btn">
                    <div class="form-group bottom-buttons text-right mar-b-0">
                        <button type="submit" id="apply_filter" class="btn btn-green mr15">Apply Filter</button>
                        <button type="button" class="btn btn-red" id="filter-cancel">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>




<div class="tpv-contbx edit-agentinfo">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12 pdlr0">
                        <div class="client-bg-white">
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <h1>My Leads</h1>
                                </div>
                                <div class="edit_twilio">
                                    <div class="col-xs-12 col-sm-9 col-md-9 sor_fil">
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container">
                                                    <button type="button">{!! getimage('images/search.png') !!}</button>
                                                    <input placeholder="Search" class="search_text" id="lead_search" name="search_leads" type="text" value="">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-group pull-right mr15">
                                            <div id="filter-wrapper" class="select-filter-wrapper">
                                                <a href="javascript:void(0)" class="reset-link">Reset Filter</a>
                                                <label>
                                                    Filter <span class="filter-selected"></span>
                                                    <span class="filter-icon"><a href="#"><img src="images/filter-icon.png"></a></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 tpv_heading">
                                @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                                @endif                                
                                @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    <p>{{session()->get('error')}} </p>
                                </div>
                                @endif
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
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                                    <div class="table-responsive">
                                        <table class="table" id="leads-table">
                                            <thead>
                                                <tr class="heading">
                                                    <th>Sr. No.</th>
                                                    <th>Reference ID</th>
                                                    <th>Associated Lead ID</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th class=w80>Commodity</th>
                                                    <th>City</th>
                                                    <th>State</th>
                                                    <th class="action-width" style="text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--delete-lead--popup---start--->
        <div class="modal fade confirmation-model" id="cancel_lead">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('telesale_cancellead')}}" method="POST" id="cancel_lead_from">
                        {{ csrf_field() }}
                        <div class="ajax-response"></div>
                        <input type="hidden" name="lead_id" id="lead_to_cancel">
                        <input type="hidden" name="previous_url" value="{{url()->full()}}">

                        <div class="modal-body">
                            <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                            <div class="mt20 text-center">
                               This lead will be canceled.
                            </div>

                            <div class="form-group deactivate-reason cancel-label pd15">
                                <label for="styled-checkbox-1">Please provide a reason for cancellation:</label>
                                <textarea class="form-control" rows="5" name="reason" id="reason"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btnintable bottom_btns pd0">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-green">Confirm</button>
                                    <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--delete-lead--popup---end--->

        <!--clone-lead--popup---start--->
        <div class="modal fade confirmation-model" id="clone_lead">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                        This lead will be cloned.
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
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        today = new Date();
            usaTime = today.toLocaleString("en-US", {timeZone: "{{Auth::user()->timezone}}"});
            today = new Date(usaTime);
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            today = mm + '/' + dd + '/' + yyyy;
            
            $('#date_start').daterangepicker({
                autoUpdateInput: true,
                startDate: firstDay,
                endDate: today,
                maxDate: today
            });
        // this is for ajax datatable clicking on pagination button
        $('body').on('click','.dataTables_paginate .paginate_button',function(){     
            $('html, body').animate({
                scrollTop: $(".sales_tablebx").offset().top
            }, 400);
        });
    $('#filter-wrapper label').click(function() {
        $('.select-filter').removeClass('hide');
        $('body').addClass('fixed-body');
    });
    $('#filter-back,#filter-cancel').click(function() {
        $('.select-filter').addClass('hide');
        $('body').removeClass('fixed-body');
    });

    var data = {};
    data.status = "{{ Request::get('status') }}";
    data.date_range = "{{ Request::get('date_range') }}";
    data.state = "{{ Request::get('state') }}";
    getLeads(data);

    if (data.status === "" && data.state === '' && data.date_range === '') {
        $('.filter-selected').addClass('hide');
        $('.reset-link').addClass('hide');
    } else {
        $('.filter-selected').removeClass('hide');
        $('.reset-link').removeClass('hide');
    }

    $('.reset-link').click(function() {
        var obj = {
            Title: "Laravel",
            Url: "{{ route('profile.leads') }}"
        };
        window.history.pushState(obj, obj.Title, obj.Url);
        $("input[name=status]:checked").attr('checked', false);
        $("input[name=date_range]").val('');
        $("input[name=state]:checked").attr('checked', false);
        $('.select-filter').addClass('hide');
        $('.reset-link').addClass('hide');
        $('body').removeClass('fixed-body');
        $('.filter-selected').addClass('hide');
        var data = {};
        getLeads(data);
    });


    function getLeads(data) {
        var leadsTable = $('#leads-table').DataTable({
            /*sDom: "ltipr",*/
            dom: 'tr<"bottom"lip>',
            bProcessing: true,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            lengthChange: true,
            searchDelay: 1000,
            //searching: false,
            ajax: {
                url: "{{ route('profile.leads.ajax') }}",
                data: data
            },
            aaSorting: [
                [3, 'desc']
            ],
            columns: [{
                    data: null,
                    orderable: false,
                },
                {
                    data: 'refrence_id',
                    name: 'refrence_id'
                },
                {
                    data: 'multiple_parent_id',
                    name: 'multiple_parent_id'
                },
                
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'date',
                    name: 'created_at'
                },
                {
                    data: 'time',
                    name: 'created_at'
                },
                {
                    data: 'commodities',
                    searchable: false
                },
                {
                    data: 'city',
                    name: 'zip_codes.city',
                },
                {
                    data: 'state',
                    name: 'zip_codes.state',
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                "searchable": false,
                "orderable": false,
                "width": "5%",
                "targets": 0,
            }],
            'fnDrawCallback': function() {
                var table = $('#leads-table').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#leads-table_info')[0].style.display = 'block';
                    $('#leads-table_paginate')[0].style.display = 'block';
                } else {
                    $('#leads-table_info')[0].style.display = 'none';
                    $('#leads-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#leads-table_length')[0].style.display = 'none';
                } else {
                    $('#leads-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#leads-table').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        });
        

        $('#lead_search').change(delay(function(e) {
            leadsTable.search($(this).val()).draw();
        },500))
    }

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
    $('body').on('click', '.clone_lead', function(e) {

        var url = $_this(this).data('url');
        console.log(url);
        var ref_id = $_this(this).data('refid');
        if (url == "" && ref_id == "") {
            e.preventDefault();
            return false;
        } else {
            $('#lead_clone_confirm').attr('href', url);
            $('#ref_id_digit_to_clone').html(ref_id);
        }
    });
    

    /*
      @Author : Amit Amreliya
      @Desc   : Auto search for state select in filter
      @Input  : 
      @Output : 
      @Date   : 23/01/2020
    */

    function myFunction() {
        var input, filter, ul, li, label, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("myUL");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("label")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
});
</script>




@endpush
