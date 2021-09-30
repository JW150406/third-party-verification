@push('styles')
<style type="text/css">
		
	   .agent-avail-report-client-class tbody tr td {
            font-family: "DINRegular", sans-serif !important;
        }
        .agent-avail-report-client-class thead tr th {
            font-weight: 700 !important;
            font-family: "DINRegular", sans-serif !important;
            padding: 0px 8px !important;
            background-color: #3a58a8 !important;
            color: #fff !important;
            text-align: center !important;            
            height: 40px;
        }
        
        .agent-avail-report-client-class tbody tr td:first-child {
            font-weight: 500 !important;
            color: #3a58a8 !important;
        }
        .agent-avail-report-client-class tbody tr td:not(:first-child) {
            color: #000;
            text-align: center;
		}
		
		#ajax-agent-avail .dataTables_scrollHead .dataTables_scrollHeadInner{
            width:100% !important;
        }

        #ajax-agent-avail .dataTables_scroll{
            margin: 0px;
            padding: 0px;
        }
        #ajax-agent-avail .dataTables_wrapper.no-footer .dataTables_scrollBody{
            border-bottom: none !important;
		}

       
</style>
@endpush
<div class="dashboard-box">
	<span class="dashboard-spiner-icon" id="agent-avail-report-client">
		<i class="fas fa-circle-notch fa-spin" aria-hidden="true "></i>
	</span>
    <h4 class="dash-hd-title">Agent's Availability (Client Wise)</h4>
    <div class="sor_fil utility-btn-group mr15 mb5">
        <button type="button" class="form-control" onclick="refreshAgentAvailReport();"><i
                    class="prev-nex-btn fa fa-refresh fa-3x" aria-hidden="true"></i>
		</button>
    </div>
    <div  style="width: 100%; height: 320px;" id="ajax-agent-avail">
        <table class="table agent-avail-report-client-class" id="agent-avail-report-client-update" style="width: 100%;">            
        </table>
    </div>
</div>

@push('scripts')
    <script>
		$(document).ready(function() {            
			$('#ajax-agent-avail .dataTables_wrapper.no-footer .dataTables_scrollBody').addClass('scroller');
		});
		loadAgentAvailReport();

		function loadAgentAvailReport() {
			var leadTable = $('#agent-avail-report-client-update').DataTable({
				// dom: 'Rtr<"bottom"lip>',
				colReorder: {
					allowReorder: false
				},
				processing: true,
				serverSide: true,
				bDestroy: true,
				searchDelay: 1000,
				autoWidth: true,
				lengthChange: false,
				bPaginate: false,
				bFilter: false,
				bInfo: false,
				ajax: {
					url: "{{ route('agentdashboard.get-agent-details-client-wise') }}",
					method: "get",
				},
				columns: [
					{
						data: 'client_name',
						title: 'Client',
						orderable: true,
						searchable: true
					},
					{
						data: 'availableWorkerCount',
						title: 'Available',
						orderable: true,
						searchable: true
					},
					{
						data: 'unavailableWorkerCount',
						title: 'On Call',
						orderable: true,
						searchable: true
					},
					{
						data: 'wrapUpWorkerCount',
						title: 'Wrap Up',
						orderable: true,
						searchable: true
					},
					{
						data: 'notAvailableWorkerCount',
						title: 'Not Available',
						orderable: true,
						searchable: true
					}
				],
				columnDefs: [
					{
						"searchable": false,
						"orderable": false,
						"width": "10%",
						"targets": 0
					},
					{
						"searchable": false,
						"orderable": false,
						"width": "10%",
						"targets": 1
					},
					{
						"searchable": false,
						"orderable": false,
						"width": "10%",
						"targets": 2
					},
					{
						"searchable": false,
						"orderable": false,
						"width": "10%",
						"targets": 3
					},
					{
						"searchable": false,
						"orderable": false,
						"width": "10%",
						"targets": 4
					}
				],
			});
		}

		function refreshAgentAvailReport() {
			$('#agent-avail-report-client-update').DataTable().ajax.reload();
		}		

    </script>
@endpush