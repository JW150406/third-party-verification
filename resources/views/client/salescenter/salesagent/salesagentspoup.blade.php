<div class="modal fade confirmation-model" id="DeletesalescenterSalesagent">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('client.salescenter.salesagent.updatestatus',['client_id' => $client_id, 'salescenter_id'=>$salecenter_id  ])}}" method="POST" id="action-for-salesagent">
        <input type="hidden" value="" name="status" id="status_to_change">
        <input type="hidden" value="" name="userid" id="userid">
        {{ csrf_field() }}
        {{ method_field('POST') }}

       

        <div class="modal-body">
        <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
          Are you sure you want to <span class="status-to-change-text"></span> <strong class="status-change-salescentersaleuser"></strong>?

          <div class="clearfix"></div>
          <div class="form-group reason-container " style="margin-top:15px">
            <label for="reason_for_deactivation">Hire options</label>
            <select name="hireoptions" class="selectmenu form-control" id="hireoptions" required>
              <option value="Rehire">Rehire</option>
              <option value="No Rehire">No Rehire</option>
            </select>
          </div>

          <div class="form-group reason-container ">
            <label for="reason_for_deactivation">Reason for deactivate agent</label>
            <textarea class="form-control resonfordeactivate" required placeholder="Reason" name="resonfordeactivate"></textarea>
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
<script>
  $('body').on('click', '.deactivate-salescentersaleuser', function(e) {
    $('#DeletesalescenterSalesagent').modal();
    var uid = $(this).data('uid');
    var salescentersaleuser = $(this).data('salescentersaleuser');
    $('#DeletesalescenterSalesagent #userid').val(uid);
    $('#status_to_change').val('inactive');
    $('.status-to-change-text').html('deactivate');
    $('.reason-container').show();
    $('.resonfordeactivate').val('');
    $('.resonfordeactivate').attr('required', 'required');

    $('.status-change-salescentersaleuser').html(salescentersaleuser);


  });
  $('body').on('click', '.delete-salescentersaleuser', function(e) {
    $('#DeletesalescenterSalesagent').modal();
    var uid = $(this).data('uid');
    var salescentersaleuser = $(this).data('salescentersaleuser');
    $('#DeletesalescenterSalesagent #userid').val(uid);
    $('#status_to_change').val('delete');
    $('.status-to-change-text').html('Delete');
    $('.status-change-salescentersaleuser').html(salescentersaleuser);
    $('.reason-container').hide();
    $('.resonfordeactivate').val('');
    $('.resonfordeactivate').removeAttr('required');


  });
  $('body').on('click', '.activate-salescentersaleuser', function(e) {
    $('#DeletesalescenterSalesagent').modal();
    var uid = $(this).data('uid');
    var salescentersaleuser = $(this).data('salescentersaleuser');
    $('#DeletesalescenterSalesagent  #userid').val(uid);
    $('#status_to_change').val('active');
    $('.status-to-change-text').html('activate');
    $('.status-change-salescentersaleuser').html(salescentersaleuser);
    $('.reason-container').hide();
    $('.resonfordeactivate').val('');
    $('.resonfordeactivate').removeAttr('required');
  });
</script>