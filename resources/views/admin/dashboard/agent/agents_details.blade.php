@push('styles')
<style type="text/css">

		ul.tabs{
			margin: 0px;
			padding: 0px;
			list-style: none;
		}
		ul.tabs li{
			background: none;
			color: #222;
			display: inline-block;
			padding: 10px 15px;
			cursor: pointer;
		}

		ul.tabs li.current{
			background: #ededed;
			color: #222;
		}

		.tab-content{
			/* display: none;
			background: #ededed;
            padding: 15px; */
            /* new */
            margin: 0;
            padding:0;
            display: none;
			background: #ededed;            
        }

		.tab-content.current{
			display: inherit;
        }
     
        .agent-details-table tbody tr td {
            font-family: "DINRegular", sans-serif !important;
        }
        .agent-details-table thead tr th {
            font-weight: 700 !important;
            font-family: "DINRegular", sans-serif !important;
            padding: 0px 8px !important;
            background-color: #3a58a8 !important;
            color: #fff !important;
            text-align: center !important;
            width: 20% !important;
            height: 40px;
        }

        .agent-details-table tbody tr td {
            width: 25% !important;
        }
        #agent-details-table thead tr th {
            width: 40px !important;
        }
        
        .agent-details-table tbody tr td:first-child {
            font-weight: 500 !important;
            color: #3a58a8 !important;
        }
        .agent-details-table tbody tr td:not(:first-child) {
            color: #000;
            text-align: center;
        }
        #ajax-agent-detail .dataTables_scrollHead .dataTables_scrollHeadInner{
            width:100% !important;
        }

        #ajax-agent-detail .dataTables_scroll{
            margin: 0px;
            padding: 0px;
        }
        #ajax-agent-detail .dataTables_wrapper.no-footer .dataTables_scrollBody{
            border-bottom: none !important;

        }
</style>
@endpush
<div class="dashboard-box">
	<span class="dashboard-spiner-icon" id="twilio-task-progress-loading">
		<i class="fas fa-circle-notch fa-spin" aria-hidden="true "></i>
	</span>
    <h4 class="dash-hd-title">Agent's Availability</h4>
    <div class="sor_fil utility-btn-group mr15">
        <button type="button" class="form-control" onclick='refreshAgentsAvailability();'> <i class="prev-nex-btn fa fa-refresh fa-3x" aria-hidden="true"></i> </button>
    </div>
    <div style="width: 100%; height: 320px" id="ajax-agent-detail">            
        {{-- ajax-content-here  --}}
    </div>
</div>
@push('scripts')
    <script>
        
        $(document).ready(function() {            
            getAgentDetails();
            
        });

        function getAgentDetails(){  
            var tabId = $('.tab-content.current').attr('id');

            $.ajax({
                type: "GET",
                url: "{{ route('agentdashboard.get-agent-detail') }}",                
                data:{ 'tabId' : tabId },
                success: function(data){
                    $('#ajax-agent-detail').html(data);
                    $('ul.tabs li').click(function(){
                        var tab_id = $(this).attr('data-tab');

                        $('ul.tabs li').removeClass('current');
                        $('.tab-content').removeClass('current');

                        $(this).addClass('current');
                        $("#"+tab_id).addClass('current');  
                    });
                    
                    var table = $('#twilio-agent-available').DataTable({searching: false, paging: false, info: false, "sScrollCollapse": true,});
                    var table = $('#twilio-agent-oncall').DataTable({searching: false, paging: false, info: false, "sScrollCollapse": true});
                    var table = $('#twilio-agent-wrapup').DataTable({searching: false, paging: false, info: false, "sScrollCollapse": true});
                    var table = $('#twilio-agent-notavailable').DataTable({searching: false, paging: false, info: false, "sScrollCollapse": true});
                    
                    // $('table').DataTable({searching: false, paging: false});
                },
                error: function(xhr, status, error){
                    console.error(xhr);
                }
            });
        }

        function refreshAgentsAvailability(){            
            getAgentDetails();

            
            // $('#twilio-agent-available').DataTable().ajax.reload();
            // $('#twilio-agent-oncall').DataTable().ajax.reload();
            // $('#twilio-agent-wrapup').DataTable().ajax.reload();
            // $('#twilio-agent-notavailable').DataTable().ajax.reload();
        }
    </script>
@endpush