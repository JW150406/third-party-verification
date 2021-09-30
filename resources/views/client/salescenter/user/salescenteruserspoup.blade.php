<div class="modal fade confirmation-model" id="DeletesalescenterUser">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('client.salescenter.user.updatestatus',['client_id' => $client_id, 'salescenter_id'=>$salescenter_id  ])}}" method="POST">
        <input type="hidden" value="" name="status" id="status_to_change">
        <input type="hidden" value="" name="userid" id="userid">
        {{ csrf_field() }}
        {{ method_field('POST') }}

        <!-- <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"> User Action</h4>
				</div> -->

        <div class="modal-body">
          Are you sure you want to <span class="status-to-change-text"></span> <strong class="status-change-salescenteruser"></strong>.
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Confirm</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $('body').on('click', '.deactivate-salescenteruser', function(e) {
    $('#DeletesalescenterUser').modal();
    var uid = $(this).data('uid');
    var salescenteruser = $(this).data('salescenteruser');
    $('#DeletesalescenterUser #userid').val(uid);
    $('#status_to_change').val('inactive');
    $('.status-to-change-text').html('deactivate');
    $('.status-change-salescenteruser').html(salescenteruser);


  });
  $('body').on('click', '.activate-salescenteruser', function(e) {
    $('#DeletesalescenterUser').modal();
    var uid = $(this).data('uid');
    var salescenteruser = $(this).data('salescenteruser');
    $('#DeletesalescenterUser  #userid').val(uid);
    $('#status_to_change').val('active');
    $('.status-to-change-text').html('activate');
    $('.status-change-salescenteruser').html(salescenteruser);
  });
</script>