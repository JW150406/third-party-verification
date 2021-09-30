<div class="dashboard-box">
	<span class="dashboard-spiner-icon" id="twilio-task-progress-loading">
		<i class="fas fa-circle-notch fa-spin" aria-hidden="true "></i>
	</span>
    <h4 class="dash-hd-title">Ongoing Calls</h4>
    <div class="sor_fil utility-btn-group mr15">
        <button type="button" class="form-control" onclick="refreshTwilioTaskProgressReport();"><i
                    class="prev-nex-btn fa fa-refresh fa-3x" aria-hidden="true"></i></button>
    </div>
    <div style="width: 100%;">
        <table class="table ongoing-calls-table-class" id="twilio-task-progress-update" style="width: 100%;">
            <thead class="">
            <tr>
                <th class="dashboard-table-color">Agent Name</th>
                <th class="dashboard-table-color">Lead ID</th>
                <th class="dashboard-table-color">Call Type</th>
                <th class="dashboard-table-color">Client Name</th>
                <th class="dashboard-table-color">Progress</th>
            </tr>
            </thead>
            <tbody id="twilio-task-progress-report" class="scroller">
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script>
		loadTwilioLeadTaskReport();

		function loadTwilioLeadTaskReport() {
			var leadTable = $('#twilio-task-progress-update').DataTable({
				dom: 'Rtr<"bottom"lip>',
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
					url: "{{ route('agent.dashboard.twilio-task-progress') }}",
					method: "get",
				},
				language: {
					emptyTable: "No ongoing call available"
				},
				columns: [
					{
						data: 'TPVAgent',
						orderable: false,
						searchable: false
					},
					{
						data: 'refrence_id',
						orderable: false,
						searchable: false
					},
					{
						data: 'Method',
						orderable: false,
						searchable: false
					},
					{
						data: 'Client',
						orderable: false,
						searchable: false
					},
					{
						data: 'task_progress',
						orderable: false,
						searchable: false
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
						"width": "60%",
						"targets": 4
					}
				],
			});
		}

		function refreshTwilioTaskProgressReport() {
			$('#twilio-task-progress-update').DataTable().ajax.reload();
		}
    </script>
@endpush